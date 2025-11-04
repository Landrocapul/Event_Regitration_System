document.addEventListener('DOMContentLoaded', () => {
    // --- DATABASE SIMULATION ---
    // Start with a few sample participants
    let participants = [
        { id: 1, fullname: 'Juan Dela Cruz', email: 'juan.delacruz@example.com' },
        { id: 2, fullname: 'Maria Santos', email: 'maria.santos@example.com' },
        { id: 3, fullname: 'Andres Reyes', email: 'andres.reyes@example.com' },
        { id: 4, fullname: 'Sofia Garcia', email: 'sofia.garcia@example.com' },
    ];
    let nextId = 5; // To assign unique IDs to new participants
    const maxParticipants = 10;

    // --- ELEMENT SELECTORS ---
    const registrationForm = document.getElementById('registration-form');
    const editForm = document.getElementById('edit-form');
    const tableBody = document.getElementById('participants-table-body');
    const registrationColumn = document.getElementById('registration-column');
    const editColumn = document.getElementById('edit-column');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const spotsRemainingEl = document.getElementById('spots-remaining');
    const messageArea = document.getElementById('message-area');

    // --- FUNCTIONS ---

    // Function to render the participants table
    const renderTable = () => {
        tableBody.innerHTML = ''; // Clear existing table rows

        if (participants.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" style="text-align:center;">No participants yet.</td></tr>';
            return;
        }

        participants.forEach(p => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${p.id}</td>
                <td>${p.fullname}</td>
                <td>${p.email}</td>
                <td class="action-links">
                    <button class="btn btn-action btn-success btn-edit" data-id="${p.id}">Edit</button>
                    <button class="btn btn-action btn-delete" data-id="${p.id}">Delete</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    };

    // Function to show/hide registration form and message
    const updateRegistrationView = () => {
        const spotsLeft = maxParticipants - participants.length;
        if (spotsLeft > 0) {
            registrationForm.style.display = 'block';
            spotsRemainingEl.innerHTML = `<b>${spotsLeft}</b> spot${spotsLeft > 1 ? 's' : ''} remaining.`;
            // Remove full message if it exists
            const fullMessage = registrationColumn.querySelector('.message');
            if(fullMessage) fullMessage.remove();
        } else {
            registrationForm.style.display = 'none';
            spotsRemainingEl.innerHTML = '';
            if(!registrationColumn.querySelector('.message')) {
                const fullMessage = document.createElement('div');
                fullMessage.className = 'message message-info';
                fullMessage.textContent = 'Registration is Full!';
                registrationColumn.appendChild(fullMessage);
            }
        }
    };
    
    // Function to show a temporary message
    const showMessage = (text, type = 'success') => {
        messageArea.innerHTML = `<div class="message message-${type}">${text}</div>`;
        setTimeout(() => { messageArea.innerHTML = ''; }, 3000);
    };

    // --- EVENT LISTENERS ---

    // Handle new participant registration
    registrationForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if (participants.length >= maxParticipants) {
            showMessage('Registration is full!', 'error');
            return;
        }
        
        const fullname = document.getElementById('fullname').value;
        const email = document.getElementById('email').value;

        participants.push({ id: nextId++, fullname, email });
        
        registrationForm.reset();
        showMessage('Registration successful!');
        renderTable();
        updateRegistrationView();
    });

    // Handle Edit and Delete button clicks using event delegation
    tableBody.addEventListener('click', (e) => {
        const target = e.target;
        const id = parseInt(target.getAttribute('data-id'));

        // Handle DELETE
        if (target.classList.contains('btn-delete')) {
            if (confirm('Are you sure you want to delete this participant?')) {
                participants = participants.filter(p => p.id !== id);
                renderTable();
                updateRegistrationView();
                showMessage('Participant deleted.', 'error');
            }
        }

        // Handle EDIT
        if (target.classList.contains('btn-edit')) {
            const participant = participants.find(p => p.id === id);
            if (participant) {
                document.getElementById('edit-id').value = participant.id;
                document.getElementById('edit-fullname').value = participant.fullname;
                document.getElementById('edit-email').value = participant.email;
                editColumn.classList.remove('hidden');
            }
        }
    });
    
    // Handle the update form submission
    editForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = parseInt(document.getElementById('edit-id').value);
        const fullname = document.getElementById('edit-fullname').value;
        const email = document.getElementById('edit-email').value;

        const participantIndex = participants.findIndex(p => p.id === id);
        if(participantIndex > -1) {
            participants[participantIndex] = { id, fullname, email };
        }
        
        editColumn.classList.add('hidden');
        renderTable();
        showMessage('Participant updated successfully!');
    });

    // Handle Cancel Edit button
    cancelEditBtn.addEventListener('click', () => {
        editColumn.classList.add('hidden');
    });

    // --- INITIAL RENDER ---
    renderTable();
    updateRegistrationView();
});