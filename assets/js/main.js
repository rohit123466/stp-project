/**
 * main.js
 * Client-side validation for the public contact form.
 * This is a first line of defense for user experience only --
 * the server in contact.php re-validates everything before saving.
 */
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        var name = document.getElementById('name');
        var email = document.getElementById('email');
        var phone = document.getElementById('phone');
        var message = document.getElementById('message');
        var isValid = true;

        clearErrors(form);

        if (name.value.trim().length < 2) {
            showError(name, 'Please enter your full name.');
            isValid = false;
        }

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email.value.trim())) {
            showError(email, 'Please enter a valid email address.');
            isValid = false;
        }

        if (phone.value.trim() !== '' && !/^[0-9+\-\s()]{7,20}$/.test(phone.value.trim())) {
            showError(phone, 'Please enter a valid phone number.');
            isValid = false;
        }

        if (message.value.trim().length < 10) {
            showError(message, 'Message should be at least 10 characters long.');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    function showError(field, text) {
        field.classList.add('is-invalid');
        var feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) feedback.textContent = text;
    }

    function clearErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
    }
});

/**
 * Image preview for admin add/edit forms.
 * Any <input type="file"> with data-preview="#someId" will update that
 * element's src attribute as soon as a file is chosen.
 */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
        input.addEventListener('change', function () {
            var previewEl = document.querySelector(input.getAttribute('data-preview'));
            if (!previewEl || !input.files || !input.files[0]) return;
            previewEl.src = URL.createObjectURL(input.files[0]);
        });
    });
});
