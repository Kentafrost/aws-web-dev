fetch('../fileList.json')
  .then(res => res.json())
  .then(files => {
    const tbody = document.querySelector('#linkTable tbody');
    files.forEach(file => {
      if (file.endsWith('.html') && file.startsWith('websites')) {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${file}</td>
          <td><a href="${file}" target="_blank">開く</a></td>
        `;
        tbody.appendChild(row);
      }
    });
  })
  .catch(err => {
    const tbody = document.querySelector('#linkTable tbody');
    tbody.innerHTML = `<tr><td colspan="2" style="color:red;">読み込みエラー: ${err.message}</td></tr>`;
    console.error("Fetch error:", err);
  });