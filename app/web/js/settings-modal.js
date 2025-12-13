document.addEventListener('DOMContentLoaded', () => {
    const settingsBtn = document.getElementById('settings-btn');
    const settingsModal = document.getElementById('settings-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const cancelSettingsBtn = document.getElementById('cancel-settings-btn');
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');

    if (!settingsBtn || !settingsModal) return;

    function openSettingsModal() {
        settingsModal.classList.add('open');
    }

    function closeSettingsModal() {
        settingsModal.classList.remove('open');
    }

    settingsBtn.addEventListener('click', openSettingsModal);
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeSettingsModal);
    if (cancelSettingsBtn) cancelSettingsBtn.addEventListener('click', closeSettingsModal);

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});