// Load existing players on page load and add them to the table
async function loadPlayers() {
    try {
        const response = await fetch('get_players.php');
        if (!response.ok) throw new Error('Network error when loading players');

        const result = await response.json();
        if (!result.success) {
            alert('Failed to load players: ' + result.message);
            return;
        }

        result.players.forEach(player => addPlayerToTable(player));
    } catch (error) {
        console.error(error);
        alert('Error loading players.');
    }
}

// Add player row to the table
function addPlayerToTable(player) {
    const table = document.querySelector('table');
    const newRow = document.createElement('tr');
    newRow.dataset.playerId = player.id;

    const rowCount = table.rows.length; // Includes the header, adjust if needed
    const rowNumber = rowCount; // Assumes header is the first row

    newRow.innerHTML = `
        <td>${rowNumber}</td>
        <td>${player.name}</td>
        <td>${player.position}</td>
        <td>${player.jersey_number}</td>
        <td>${player.nationality || ''}</td>
        <td>${player.birthdate || ''}</td>
        <td>
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
        </td>
    `;

    table.appendChild(newRow);
}


// Load players when DOM is ready
window.addEventListener('DOMContentLoaded', loadPlayers);

// Add player via form submit
document.querySelector('form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch('add_player.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) throw new Error('Network error');

        const result = await response.json();

        if (result.success) {
            addPlayerToTable(result.player);
            form.reset();
            alert('Player added successfully!');
        } else {
            alert('Add failed: ' + result.message);
        }
    } catch (error) {
        console.error(error);
        alert('An error occurred while adding the player.');
    }
});

// Event delegation for Edit and Delete buttons in the table
document.querySelector('table').addEventListener('click', async (event) => {
    const row = event.target.closest('tr');
    if (!row) return;

    const playerId = row.dataset.playerId;
    if (!playerId) return;

    // DELETE player
    if (event.target.classList.contains('delete-btn')) {
        if (!confirm(`Are you sure you want to delete this player?`)) return;

        try {
            const response = await fetch('delete_player.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: playerId })
            });
            const result = await response.json();

            if (result.success) {
                row.remove();
                alert('Player deleted.');
            } else {
                alert('Delete failed: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Error deleting player.');
        }
    }

    // EDIT player
    if (event.target.classList.contains('edit-btn')) {
        const cells = row.querySelectorAll('td');
        const name = prompt('Name:', cells[1].textContent);
        const position = prompt('Position:', cells[2].textContent);
        const jersey_number = prompt('Jersey Number:', cells[3].textContent);
        const nationality = prompt('Nationality:', cells[4].textContent);
        const birthdate = prompt('Birthdate (YYYY-MM-DD):', cells[5].textContent);

        if (!name || !position || !jersey_number || !nationality || !birthdate) {
            alert('All fields are required.');
            return;
        }

        const updatedPlayer = {
            id: parseInt(playerId),
            name,
            position,
            jersey_number: parseInt(jersey_number),
            nationality,
            birthdate
        };

        try {
            const response = await fetch('edit_player.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(updatedPlayer)
            });

            const result = await response.json();

            if (result.success) {
                cells[1].textContent = name;
                cells[2].textContent = position;
                cells[3].textContent = jersey_number;
                cells[4].textContent = nationality;
                cells[5].textContent = birthdate;
                alert('Player updated!');
            } else {
                alert('Edit failed: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Error updating player.');
        }
    }
});
