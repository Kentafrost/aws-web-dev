console.log('Loading file list...');

fetch('./fileList.json')
  .then(res => {
    console.log('Response status:', res.status);
    if (!res.ok) {
      throw new Error(`HTTP ${res.status}: ${res.statusText}`);
    }
    return res.json();
  })
  .then(files => {
    console.log('Files loaded:', files);
    const tbody = document.querySelector('#linkTable tbody');
    
    if (!tbody) {
      console.error('Table body not found!');
      return;
    }
    
    let addedCount = 0;
    files.forEach(file => {
      if (file.endsWith('.html') && file.startsWith('websites')) {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${file}</td>
          <td><a href="${file}">開く</a></td>
        `;
        tbody.appendChild(row);
        addedCount++;
      }
    });
    
    console.log(`Added ${addedCount} links to the table`);
    
    if (addedCount === 0) {
      tbody.innerHTML = `<tr><td colspan="2" style="color:orange;">該当するHTMLファイルが見つかりませんでした</td></tr>`;
    }
  })
  .catch(err => {
    console.error("Fetch error:", err);
    const tbody = document.querySelector('#linkTable tbody');
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="2" style="color:red;">読み込みエラー: ${err.message}<br>ファイルパスを確認してください</td></tr>`;
    }
  });
