from flask import Flask, request, jsonify, render_template, send_from_directory
import json
import pandas as pd
import requests
import os
from bs4 import BeautifulSoup
import time, sys
import csv, logging

# parent directory
parent_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
sys.path.append(parent_dir)

import common

# Try to import secret_variables, use defaults if not available
try:
    import secret_variables
    SECRET_VARS_AVAILABLE = True
except ImportError:
    SECRET_VARS_AVAILABLE = False
    print("Warning: secret_variables.py not found. Using default URLs.")


# delete all files in html folder with proper permission handling
import shutil
import stat
current_dir = os.path.dirname(os.path.abspath(__file__))
html_dir = os.path.join(current_dir, 'html')

def handle_remove_readonly(func, path, exc):
    if os.path.exists(path):
        os.chmod(path, stat.S_IWRITE)
        func(path)

def safe_cleanup_html_dir(html_dir):
    try:
        if os.path.exists(html_dir):
            # Change permissions of all files to writable before deletion
            for root, dirs, files in os.walk(html_dir):
                for file in files:
                    file_path = os.path.join(root, file)
                    try:
                        os.chmod(file_path, stat.S_IWRITE)
                    except:
                        pass
                for dir_name in dirs:
                    dir_path = os.path.join(root, dir_name)
                    try:
                        os.chmod(dir_path, stat.S_IWRITE)
                    except:
                        pass
            
            # Remove the directory
            shutil.rmtree(html_dir, onerror=handle_remove_readonly)
            logging.info(f"Successfully deleted HTML directory: {html_dir}")
        
        # Create new directory
        os.makedirs(html_dir, exist_ok=True)
        
        # Set full permissions on the new directory
        try:
            os.chmod(html_dir, stat.S_IREAD | stat.S_IWRITE | stat.S_IEXEC)
        except:
            pass
            
        logging.info(f"Created HTML directory with proper permissions: {html_dir}")
        return True
        
    except PermissionError as pe:
        logging.error(f"Permission error with HTML directory: {pe}")
        print(f"Permission error: {pe}")
        
        # Try alternative approach - create with different name first
        try:
            temp_dir = f"{html_dir}_temp"
            if os.path.exists(temp_dir):
                shutil.rmtree(temp_dir, ignore_errors=True)
            os.makedirs(temp_dir, exist_ok=True)
            
            # If temp directory creation succeeds, try to rename it
            if os.path.exists(html_dir):
                shutil.rmtree(html_dir, ignore_errors=True)
            os.rename(temp_dir, html_dir)
            
            os.chmod(html_dir, stat.S_IREAD | stat.S_IWRITE | stat.S_IEXEC)
            logging.info(f"Created HTML directory using alternative method: {html_dir}")
            return True
        except Exception as e2:
            logging.error(f"Alternative directory creation failed: {e2}")
            print(f"Could not create HTML directory: {e2}")
            return False
        
    except Exception as e:
        logging.error(f"Error handling HTML directory: {e}")
        print(f"Warning: Could not clean HTML directory. Error: {e}")
        
        # Try to create the directory anyway
        try:
            os.makedirs(html_dir, exist_ok=True)
            logging.info(f"Created HTML directory (cleanup failed): {html_dir}")
            return True
        except Exception as e2:
            logging.error(f"Failed to create HTML directory: {e2}")
            print(f"Error: Cannot create HTML directory: {e2}")
            return False

# Clean up and recreate HTML directory
print(f"Attempting to clean up HTML directory: {html_dir}")
cleanup_result = safe_cleanup_html_dir(html_dir)
if cleanup_result:
    print("✅ HTML directory cleaned up successfully")
else:
    print("⚠️ HTML directory cleanup had issues, but continuing...")

def safe_write_html_file(file_path, content):
    try:
        # Ensure directory exists
        os.makedirs(os.path.dirname(file_path), exist_ok=True)
        
        # Write the file
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        
        # Set file permissions to writable
        try:
            os.chmod(file_path, stat.S_IREAD | stat.S_IWRITE)
        except:
            pass
            
        logging.info(f"Successfully wrote HTML file: {file_path}")
        return True
        
    except Exception as e:
        logging.error(f"Error writing HTML file {file_path}: {e}")
        print(f"Warning: Could not write HTML file {file_path}: {e}")
        return False


def list_websites(search_words, max_pages, url):
    
    item_count = 0
    data_list = []
    logging.info("Listing websites...")
    
    # Handle multiple search words (should already be a list when called)
    if isinstance(search_words, str):
        search_words = [word.strip() for word in search_words.split(',')]

    for search_word in search_words:
        print(f"Searching for: {search_word}")
        
        for page_num in range(1, max_pages + 1):
            try:
                print(f"Processing page {page_num} for '{search_word}'...")
                
                # Add headers to avoid being blocked
                headers = {
                    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                }
                response = requests.get(f"{url}?word={search_word}&c=&page={page_num}", headers=headers)
                logging.info(f"Request URL: {url}?word={search_word}&c=&page={page_num}")
                logging.info(f"Response status: {response.status_code}")

                if response.status_code == 200:
                    logging.info(f"Successfully retrieved page {page_num} for '{search_word}'")
                    
                    # Parse HTML content
                    soup = BeautifulSoup(response.content, 'html.parser')
                    
                    item_titles = soup.find_all(class_="itemTitle")
                    pictures = soup.find_all(class_="itemImage")

                    for i in range(min(len(item_titles), len(pictures))):
                        item_title = item_titles[i]
                        img_src = pictures[i]
                        
                        title = item_title.get_text(strip=True)
                        
                        # skip exception words that mentioned above variables
                        skip_title = False
                        if exception_words:
                            for exception_word in exception_words:
                                if exception_word in title:
                                    logging.info(f"Skipped (contains '{exception_word}'): {title}")
                                    skip_title = True
                                    break
                        
                        if skip_title:
                            continue
                    
                        
                        # Get the URL from href attribute
                        link = item_title.find('a')
                        item_url = ""
                        
                        if link and link.has_attr('href'):
                            item_url = link.get('href')
                            if not item_url.startswith('http'):
                                item_url = url + item_url
                        
                        # Get the image URL
                        img = img_src.find('img')
                        print(img)
                        picture_url = ""
                        
                        if img and img.has_attr('src'):
                            picture_url = img.get('src')
                        print(picture_url)
                        
                        item_count += 1
                        
                        data_list.append({
                            'No': item_count,
                            'Title': title,
                            'URL': item_url,
                            'Picture': picture_url,
                            'Page_Number': page_num,
                            'Search_Word': search_word
                        })

                    logging.info(f"Page {page_num} for '{search_word}': Found {len(item_titles)} items")
                    time.sleep(1)  # Be nice to the server
                else:
                    print(f"Failed to retrieve page {page_num} for '{search_word}': {response.status_code}")
                    logging.error(f"Failed to retrieve page {page_num} for '{search_word}': {response.status_code}")

            except Exception as e:
                logging.error(f"Error on page {page_num} for '{search_word}': {str(e)}")
                continue

    return data_list

def csv_writer(data_list, output_filename, url):
    if not data_list:
        print("No data to write to CSV")
        return
    
    if url == web_url:
        category = "movie"
    elif url == anime_url:
        category = "anime"
    
    os.makedirs(csv_dir, exist_ok=True)
    csv_file_path = os.path.join(csv_dir, f'{output_filename}_{category}.csv')

    with open(csv_file_path, 'w', newline='', encoding='utf-8') as csvfile:
        fieldnames = ['No', 'Title', 'URL', 'Picture', 'Page_Number', 'Search_Word']
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        
        writer.writeheader()
        for item in data_list:
            writer.writerow(item)
    
    print(f"CSV file created: {csv_file_path}")
    return csv_file_path

def generate_html_files(output_filename, url):
    # Read HTML template
    template_path = os.path.join(current_dir, 'hina.html')
    logging.info(f"Generating HTML files using template: {template_path}")
    
    if url == web_url:
        category = "movie"
    elif url == anime_url:
        category = "anime"
    
    if not os.path.exists(template_path):
        return {"error": f"Template file not found: {template_path}"}

    with open(template_path, 'r', encoding='utf-8') as template_file:
        template_html = template_file.read()
        
        if '{content}' not in template_html:
            return {"error": "Template does not contain {content} placeholder"}

    # make HTML file using data in CSV file
    csv_file = os.path.join(csv_dir, f'{output_filename}_{category}.csv')
    print(f"Looking for CSV file: {csv_file}")
    
    if not os.path.exists(csv_file):
        print(f"CSV file not found: {csv_file}")
        # List available CSV files for debugging
        if os.path.exists(csv_dir):
            available_files = [f for f in os.listdir(csv_dir) if f.endswith('.csv')]
            print(f"Available CSV files: {available_files}")
        return {"error": f"CSV file not found: {csv_file}"}
        
    try:
        df = pd.read_csv(csv_file)
        print(f"CSV file loaded successfully. Rows: {len(df)}")
    except Exception as e:
        print(f"Error reading CSV file: {e}")
        return {"error": f"Error reading CSV file: {e}"}
        
    files_created = []

    grouped = df.groupby('Page_Number')

    # Create individual HTML files for each page
    for page_num, group in grouped:
        content_html = ""  # reset each page
        print(f"Creating HTML for page {page_num} with {len(group)} items")
        
        # Group by search word within the page
        search_words_in_page = group.groupby('Search_Word')
        
        for search_word, search_group in search_words_in_page:
            content_html += f"<h2>検索ワード: {search_word}</h2>"
            
            for index, row in search_group.iterrows():
                title = row['Title']
                url = row['URL']
                picture_url = row['Picture']
                item_no = row['No']

                content_html += f"""
                <div class="card">
                    <h3>
                        <a href="{url}">{title}</a>
                    </h3>
                    <h3><img src="{picture_url}" alt="{title}" /></h3>
                </div>
                """

        # replace {content} in HTML template with actual content for this page
        final_html = template_html.replace('{content}', content_html)
        final_html = final_html.replace('{page_number}', f"ページ {page_num}")

        # save it into the output HTML file for this specific page
        output_html_path = os.path.join(html_dir, f'{output_filename}_{category}_page_{page_num}.html')
        os.makedirs(os.path.dirname(output_html_path), exist_ok=True)

        try:
            with open(output_html_path, 'w', encoding='utf-8') as f:
                f.write(final_html)
                files_created.append(f'{output_html_path}')
                print(f"Created HTML file: {output_html_path}")
        except Exception as e:
            print(f"Error creating HTML file {output_html_path}: {e}")

    # Create a combined HTML file with all results
    all_content_html = ""
    for search_word in df['Search_Word'].unique():
        search_data = df[df['Search_Word'] == search_word]
        all_content_html += f"<h2>検索ワード: {search_word}</h2>"
        
        for index, row in search_data.iterrows():
            title = row['Title']
            url = row['URL']
            picture_url = row['Picture']
            
            all_content_html += f"""
            <div class="card">
                <h3>
                    <a href="{url}">{title}</a>
                </h3>
                <h3><img src="{picture_url}" alt="{title}" /></h3>
            </div>
            """

    # Create combined file
    final_html = template_html.replace('{content}', all_content_html)
    final_html = final_html.replace('{page_number}', 'All Results')
    
    combined_output_path = os.path.join(html_dir, f'{output_filename}_combined.html')
    with open(combined_output_path, 'w', encoding='utf-8') as f:
        f.write(final_html)
        files_created.append(f'{output_filename}_combined.html')
    return {"files_created": files_created}


app = Flask(__name__, template_folder='./', static_folder='./', static_url_path='')
# URL and search word, change these as needed
web_url = secret_variables.get_secret_variables()[0]
anime_url = secret_variables.get_secret_variables()[1]
exception_words = secret_variables.get_secret_variables()[2]

# skip titles with exception words
if not exception_words:
    print(f"No exception words written, skipping check to except")

current_dir = os.path.dirname(os.path.abspath(__file__))
csv_dir = os.path.join(current_dir, 'csv')
html_dir = os.path.join(current_dir, 'html')
website_data_list = []

@app.route('/')
def index():
    return render_template('search_interface.html')

@app.route('/html/<filename>')
def serve_html(filename):
    return send_from_directory(html_dir, filename)

# javascript files hosting
@app.route('/js/<filename>')
def serve_js(filename):
    js_dir = os.path.join(current_dir, 'js')
    return send_from_directory(js_dir, filename)

@app.route('/fileList.json')
@app.route('/js/fileList.json')
def serve_filelist():
    js_dir = os.path.join(current_dir, 'js')
    return send_from_directory(js_dir, 'fileList.json')

@app.route('/files')
def list_files():
    try:
        # Ensure html directory exists and is accessible
        if not os.path.exists(html_dir):
            os.makedirs(html_dir, exist_ok=True)
            return jsonify({'files': []})
        
        html_files = [f for f in os.listdir(html_dir) if f.endswith('.html')]
        return jsonify({'files': html_files})
    except PermissionError as e:
        print(f"Permission error accessing html directory: {e}")
        # Try to fix permissions and retry
        try:
            os.chmod(html_dir, stat.S_IREAD | stat.S_IWRITE | stat.S_IEXEC)
            html_files = [f for f in os.listdir(html_dir) if f.endswith('.html')]
            return jsonify({'files': html_files})
        except Exception as e2:
            return jsonify({'error': f'Permission denied: {str(e2)}'}), 500
    except Exception as e:
        print(f"Error listing files: {e}")
        return jsonify({'error': str(e)}), 500

# Search endpoint from search_interface.html
@app.route('/search', methods=['POST'])

def search():
    print("=== SEARCH ENDPOINT CALLED ===")
    print(f"Request method: {request.method}")
    print(f"Request content type: {request.content_type}")
    print(f"Request form data: {request.form}")
    print(f"Request data: {request.data}")
    
    try:
        search_words = request.form.get('search_word', '').strip()
        max_pages = int(request.form.get('max_pages', 5))
        
        print(f"Received search request: words='{search_words}', max_pages={max_pages}")
        
        if not search_words:
            error_response = {"error": "検索ワードを入力してください"}
            print(f"Returning error response: {error_response}")
            return jsonify(error_response), 400

        # Convert to list if it's a comma-separated string
        if isinstance(search_words, str):
            search_words = [word.strip() for word in search_words.split(',') if word.strip()]
        
        all_web_data_list = []
        all_results = []
        files_created_count = 0
        
        js_filelist_path = os.path.join(current_dir, 'js', 'fileList.json')
        
        for search_word in search_words:
            print(f"Starting search for: {search_word}, max pages: {max_pages}")
            output_filename = f'search_results_{search_word.replace(" ", "_")}'

            # Get website data for single search word
            web_data_list = list_websites([search_word], max_pages, web_url)
            anime_data_list = list_websites([search_word], max_pages, anime_url)
            
            print(f"Found {len(web_data_list)} movie items")
            print(f"Found {len(anime_data_list)} anime items for '{search_word}'")

            if web_data_list or anime_data_list:
                if not web_data_list == []:
                    all_web_data_list.extend(web_data_list)
                if not anime_data_list == []:
                    all_results.extend(anime_data_list)

                if not all_web_data_list:
                    return jsonify({"error": "データが見つかりませんでした", "search_words": search_words}), 404
                    
                try:
                    # Save to CSV
                    csv_path = csv_writer(web_data_list, output_filename, web_url)
                    csv_path = csv_writer(anime_data_list, output_filename, anime_url)

                    print(f"CSV saved: {csv_path}")
                except Exception as e:
                    print(f"Error saving CSV: {e}")
            
                try:
                    # Generate HTML files
                    movie_files = generate_html_files(output_filename, web_url)
                    anime_files = generate_html_files(output_filename, anime_url)
                    
                    # Track files created in this operation
                    if "error" not in movie_files:
                        files_created_count += len(movie_files.get('files_created', []))
                    
                    if "error" not in anime_files:
                        files_created_count += len(anime_files.get('files_created', []))
                        
                except Exception as e:
                    print(f"Error generating HTML files: {e}")
                    return jsonify({"error": f"HTML生成エラー: {str(e)}"}), 500

                print(f"HTML files created: {movie_files.get('files_created', [])}")
                print(f"HTML files created: {anime_files.get('files_created', [])}")

                # list up all html file in HTML directory
                try:
                    if os.path.exists(html_dir):
                        html_files = [f for f in os.listdir(html_dir) if f.endswith('.html')]
                        all_results.extend(html_files)
                    else:
                        print(f"Warning: HTML directory does not exist: {html_dir}")

                except PermissionError as e:
                    print(f"Permission error accessing HTML directory: {e}")
                    try:
                        os.chmod(html_dir, stat.S_IREAD | stat.S_IWRITE | stat.S_IEXEC)
                        html_files = [f for f in os.listdir(html_dir) if f.endswith('.html')]
                        all_results.extend(html_files)
                    except Exception as e2:
                        print(f"Could not fix permissions: {e2}")
                        return jsonify({"error": f"Permission error accessing HTML directory: {str(e2)}"}), 500
            else:
                return jsonify({"error": f"No data found for search word: '{search_word}'"}), 404

        # update js/fileList.json
        with open(js_filelist_path, 'w', encoding='utf-8') as f:
            try:
                json.dump(html_files, f, ensure_ascii=False, indent=2)
                print(f"Updated fileList.json at: {js_filelist_path}")
            except Exception as e:
                print(f"Failed to update {js_filelist_path}: {e}")

        response_data = {
            'search_words': search_words,
            'max_pages': max_pages,
            'total_items': len(all_web_data_list),
            'files_created': files_created_count,
            'file_paths': html_files
        }
        
        print(f"Search completed successfully: {response_data}")
        return jsonify(response_data)
    
    except Exception as e:
        print(f"Error in search endpoint: {e}")
        import traceback
        traceback.print_exc()
        return jsonify({"error": f"サーバー内部エラー: {str(e)}"}), 500

if __name__ == '__main__':
    # Create directories if they don't exist
    os.makedirs(csv_dir, exist_ok=True)
    os.makedirs(html_dir, exist_ok=True)
    
    print(f"Starting Flask server...")
    print(f"Search interface will be available at: http://localhost:5000/")
    app.run(debug=True, host='0.0.0.0', port=5000)
