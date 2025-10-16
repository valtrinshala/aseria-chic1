"use strict";
$(document).ready(function () {
    const togglePassword = document.querySelectorAll('.toggle-password');
    togglePassword.forEach(span => {
        span.addEventListener('click', function () {
            const input = span.previousElementSibling; // Get the input before the span
            const icon = span.querySelector('i'); // Get the icon inside the span
            // Toggle the input type and change the icon
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    });
    if (localStorage.getItem("navbarScrollPos") != null && window.location.pathname !== '/admin/dashboard'){
        if (document.getElementById("kt_app_sidebar_menu_scroll")){
            document.getElementById("kt_app_sidebar_menu_scroll").scrollTop = parseInt(localStorage.getItem("navbarScrollPos"), 10)
        }
    }

    $('#kt_app_sidebar_toggle').click(function () {
        var firstIconDisplay = $('#firstIcon').css('display');
        if (firstIconDisplay === 'none') {
            $('#firstIcon').css('display', 'inline-block');
            $('#secondIcon').css('display', 'none');
            $('#sidebarText').css('display', 'block');
        } else {
            $('#firstIcon').css('display', 'none');
            $('#secondIcon').css('display', 'inline-block');
            $('#sidebarText').css('display', 'none');
        }
    });
    $('input[type="file"][name="image"], input[type="file"][name="client_icon"]').on('change', function () {
        let fileInput = this.files[0];
        if (fileInput) {
            let fileName = fileInput.name;
            let fileExtension = fileName.split('.').pop().toLowerCase();
            let allowedExtensions;
            if ($('.allow-video').length === 1){
                allowedExtensions = ['png', 'svg', 'webp', 'jpg', 'jpeg', 'webp', 'mp4', 'avi', 'mov', 'flv', 'mkv'];
            }else {
                allowedExtensions = ['png', 'svg', 'webp', 'jpg', 'jpeg'];
            }
            if (allowedExtensions.indexOf(fileExtension) === -1) {
                alert('The file is not in the allowed format.');
                $(this).val('');
            }
        }
    });
});
