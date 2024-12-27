// folders.js
const folderData = {
    "Group": ["1", "2", "3"],
    "Nature": ["1", "2", "3"],
    "Other": ["1", "2", "3"],
    "Party": ["1", "2", "3"],
    "Trip": ["1", "2", "3"],
    "Work": ["1", "2", "3"],
};

async function fetchFolderContents(folderPath) {
    try {
        const response = await fetch(folderPath);
        if (response.ok) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(await response.text(), 'text/html');
            const items = [...doc.querySelectorAll('a')]
                .map(link => link.href.split('/').pop())
                .filter(name => name !== '../'); // Exclude parent directory links
            return items;
        }
    } catch (error) {
        console.error('Error fetching folder contents:', error);
    }
    return [];
}
function loadMainFolders() {
    const mainFolderDropdown = document.getElementById('mainFolder');
    const mainFolders = Object.keys(folderData); // Get the main folder names from folderData

    mainFolderDropdown.innerHTML = '<option value="">Select Folder</option>';
    mainFolders.forEach(folder => {
        const option = document.createElement('option');
        option.value = folder;
        option.textContent = folder;
        mainFolderDropdown.appendChild(option);
    });
}
function loadSubFolders(mainFolder) {
    const subFolderDropdown = document.getElementById('subFolder');
    subFolderDropdown.innerHTML = '<option value="">Choose Subfolder</option>';

    if (mainFolder && folderData[mainFolder]) {
        const subFolders = folderData[mainFolder]; // Get the subfolders from folderData
        subFolders.forEach(folder => {
            const option = document.createElement('option');
            option.value = folder;
            option.textContent = folder;
            subFolderDropdown.appendChild(option);
        });
    }
}

async function loadFiles(mainFolder, subFolder) {
    const fileContainer = document.getElementById('file-container');
    fileContainer.innerHTML = '';

    if (mainFolder && subFolder) {
        const files = await fetchFolderContents(`/uploads/${mainFolder}/${subFolder}/`);
        if (files.length === 0) {
            fileContainer.innerHTML = '<p>No files found in the selected folder.</p>';
            return;
        }

        files.forEach(file => {
            const filePath = `/uploads/${mainFolder}/${subFolder}/${file}`;
            const fileItem = document.createElement('div');
            fileItem.className = 'col-md-3 mb-4 file-item';
            fileItem.innerHTML = `
                <div class="card">
                    ${
                        /\.(jpg|jpeg|png|gif)$/i.test(file)
                            ? `<img src="${filePath}" class="card-img-top" alt="${file}" style="height: 200px; object-fit: cover;">`
                            : `<div class="card-body text-center"><p class="card-text">File: ${file}</p></div>`
                    }
                    <div class="card-body">
                        <p class="card-text">${file}</p>
                        <a href="${filePath}" download class="btn btn-primary">Download</a>
                    </div>
                </div>
            `;
            fileContainer.appendChild(fileItem);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadMainFolders();

    const mainFolderDropdown = document.getElementById('mainFolder');
    const subFolderDropdown = document.getElementById('subFolder');

    mainFolderDropdown.addEventListener('change', () => {
        const mainFolder = mainFolderDropdown.value;
        loadSubFolders(mainFolder);
        document.getElementById('file-container').innerHTML = '';
    });

    subFolderDropdown.addEventListener('change', () => {
        const mainFolder = mainFolderDropdown.value;
        const subFolder = subFolderDropdown.value;
        loadFiles(mainFolder, subFolder);
    });
});
