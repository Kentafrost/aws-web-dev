from flask import Flask, request, jsonify, render_template, send_from_directory
import json
import boto3
import pandas as pd
import requests
import os, sys
from bs4 import BeautifulSoup
import time
import csv

def list_websites(search_words, max_pages):
    
    item_count = 0
    web_data_list = []
    
    # Handle multiple search words
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
                
                response = requests.get(f"{web_url}?word={search_word}&c=&page={page_num}", headers=headers)
                
                if response.status_code == 200:
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
                                    print(f"Skipped (contains '{exception_word}'): {title}")
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
                                item_url = web_url + item_url
                        
                        # Get the image URL
                        img = img_src.find('img')
                        print(img)
                        picture_url = ""
                        
                        if img and img.has_attr('src'):
                            picture_url = img.get('src')
                            # if not picture_url.startswith('http'):
                            #     picture_url = web_url + picture_url
                        print(picture_url)
                        
                        item_count += 1
                        
                        web_data_list.append({
                            'No': item_count,
                            'Title': title,
                            'URL': item_url,
                            'Picture': picture_url,
                            'Page_Number': page_num,
                            'Search_Word': search_word
                        })
                    
                    print(f"Page {page_num} for '{search_word}': Found {len(item_titles)} items")
                    time.sleep(1)  # Be nice to the server
                else:
                    print(f"Failed to retrieve page {page_num} for '{search_word}': {response.status_code}")
                
            except Exception as e:
                print(f"Error on page {page_num} for '{search_word}': {str(e)}")
                continue

    return web_data_list

def csv_writer(web_data_list, output_filename):
    if not web_data_list:
        print("No data to write to CSV")
        return
    
    os.makedirs(csv_dir, exist_ok=True)
    csv_file_path = os.path.join(csv_dir, f'{output_filename}.csv')
    
    with open(csv_file_path, 'w', newline='', encoding='utf-8') as csvfile:
        fieldnames = ['No', 'Title', 'URL', 'Picture', 'Page_Number', 'Search_Word']
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        
        writer.writeheader()
        for item in web_data_list:
            writer.writerow(item)
    
    print(f"CSV file created: {csv_file_path}")
    return csv_file_path

def generate_html_files(output_filename):
    # Read HTML template
    template_path = os.path.join(current_dir, 'hina.html')
    
    if not os.path.exists(template_path):
        return {"error": f"Template file not found: {template_path}"}

    with open(template_path, 'r', encoding='utf-8') as template_file:
        template_html = template_file.read()
        
        if '{content}' not in template_html:
            return {"error": "Template does not contain {content} placeholder"}

    # make HTML file using data in CSV file
    csv_file = os.path.join(csv_dir, f'{output_filename}.csv')
    if not os.path.exists(csv_file):
        return {"error": f"CSV file not found: {csv_file}"}
        
    df = pd.read_csv(csv_file)
    files_created = []

    grouped = df.groupby('Page_Number')

    # Create individual HTML files for each page
    for page_num, group in grouped:
        content_html = ""  # reset each page
        
        # Group by search word within the page
        search_words_in_page = group.groupby('Search_Word')
        
        for search_word, search_group in search_words_in_page:
            search_words_num = f"検索ワード: {search_word}_ページ {page_num}"
            content_html = ""
            
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
        final_html = final_html.replace('{page_number}', search_words_num)

        # save it into the output HTML file for this specific page
        output_html_path = os.path.join(html_dir, f'{output_filename}_page_{page_num}.html')
        os.makedirs(os.path.dirname(output_html_path), exist_ok=True)

        with open(output_html_path, 'w', encoding='utf-8') as f:
            f.write(final_html)
            files_created.append(f'{output_filename}_page_{page_num}.html')

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

    # Create an index file with page summaries
    index_content = ""
    page_stats = df.groupby('Page_Number').agg({
        'Title': 'count',
        'Search_Word': lambda x: ', '.join(x.unique())
    }).rename(columns={'Title': 'Item_Count', 'Search_Word': 'Search_Words'})
    
    for page_num in sorted(page_stats.index):
        stats = page_stats.loc[page_num]
        index_content += f"""
        <div class="card">
            <h3><a href="{output_filename}_page_{page_num}.html">ページ {page_num}</a></h3>
            <p>アイテム数: {stats['Item_Count']}</p>
            <p>検索ワード: {stats['Search_Words']}</p>
        </div>
        """
    
    index_html = template_html.replace('{content}', index_content)
    index_html = index_html.replace('{page_number}', 'Index')
    
    index_output_path = os.path.join(html_dir, f'{output_filename}_index.html')
    with open(index_output_path, 'w', encoding='utf-8') as f:
        f.write(index_html)
        files_created.append(f'{output_filename}_index.html')

    # Copy fileList.json to html directory for easier access from generated HTML files
    import shutil
    source_json = os.path.join(current_dir, 'js', 'fileList.json')
    dest_json = os.path.join(html_dir, 'fileList.json')
    if os.path.exists(source_json):
        try:
            shutil.copy2(source_json, dest_json)
            print(f"Copied fileList.json to html directory")
        except Exception as e:
            print(f"Failed to copy fileList.json: {e}")

    return {"files_created": files_created}


app = Flask(__name__, template_folder='./', static_folder='./', static_url_path='')

# URL and search word, change these as needed
web_url = ""
exception_words = [""]

# skip titles with exception words
if exception_words == []:
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
        html_files = [f for f in os.listdir(html_dir) if f.endswith('.html')]
        return jsonify({'files': html_files})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/search', methods=['POST'])
def search():
    
    search_word = request.form.get('search_word', '').strip()
    max_pages = int(request.form.get('max_pages', 5))
    output_filename = 'search_results'
    
    if not search_word:
        return jsonify({"error": "検索ワードを入力してください"}), 400

    print(f"Starting search for: {search_word}, max pages: {max_pages}")

    # Get website data
    web_data_list = list_websites(search_word, max_pages)

    if not web_data_list:
        return jsonify({"error": "データが見つかりませんでした"}), 404

    # Save to CSV
    csv_path = csv_writer(web_data_list, output_filename)
    
    # Generate HTML files
    result = generate_html_files(output_filename)

    if "error" in result:
        return jsonify(result), 500
    
    # Update fileList.json with newly created files
    html_files = [f for f in os.listdir(html_dir) if f.endswith('.html') and output_filename in f]
    
    # Update both locations - js folder and html folder
    js_filelist_path = os.path.join(current_dir, 'js', 'fileList.json')
    html_filelist_path = os.path.join(html_dir, 'fileList.json')
    
    for path in [js_filelist_path, html_filelist_path]:
        try:
            with open(path, 'w', encoding='utf-8') as f:
                json.dump(result['files_created'], f, ensure_ascii=False, indent=2)
            print(f"Updated fileList.json at: {path}")
        except Exception as e:
            print(f"Failed to update {path}: {e}")

    return jsonify({
        'search_word': search_word,
        'max_pages': max_pages,
        'total_items': len(web_data_list),
        'files_created': len(result['files_created']),
        'file_paths': result['files_created'],
        'csv_file': os.path.basename(csv_path) if csv_path else None
    })

if __name__ == '__main__':
    # Create directories if they don't exist
    os.makedirs(csv_dir, exist_ok=True)
    os.makedirs(html_dir, exist_ok=True)
    
    print(f"Starting Flask server...")
    print(f"Search interface will be available at: http://localhost:5000/")
    app.run(debug=True, host='0.0.0.0', port=5000)
