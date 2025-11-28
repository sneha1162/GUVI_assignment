$(document).ready(function() {
    // Check if user is logged in
    const token = localStorage.getItem('jwt_token');
    if (token) {
        showProfile();
        loadProfile();
    } else {
        showAuth();
    }

    // Register form submission
    $('#register-form').on('submit', function(e) {
        e.preventDefault();
        const username = $('#reg-username').val();
        const email = $('#reg-email').val();
        const password = $('#reg-password').val();

        $.ajax({
            url: 'PHP/register.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ username: username, email: email, password: password }),
            success: function(response) {
                alert('Registration successful! Please login.');
                $('#register-form')[0].reset();
            },
            error: function(xhr) {
                alert('Registration failed: ' + xhr.responseJSON.message);
            }
        });
    });

    // Login form submission
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        const username = $('#login-username').val();
        const password = $('#login-password').val();

        $.ajax({
            url: 'PHP/login.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ username: username, password: password }),
            success: function(response) {
                localStorage.setItem('jwt_token', response.token);
                showProfile();
                loadProfile();
                $('#login-form')[0].reset();
            },
            error: function(xhr) {
                alert('Login failed: ' + xhr.responseJSON.message);
            }
        });
    });

    // Profile form submission
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        const bio = $('#profile-bio').val();
        const email = $('#profile-email').val();
        const token = localStorage.getItem('jwt_token');

        $.ajax({
            url: 'PHP/profile.php',
            type: 'POST',
            contentType: 'application/json',
            headers: { 'Authorization': 'Bearer ' + token },
            data: JSON.stringify({ bio: bio, email: email }),
            success: function(response) {
                alert('Profile updated successfully!');
                loadProfile();
            },
            error: function(xhr) {
                alert('Profile update failed: ' + xhr.responseJSON.message);
            }
        });
    });

    // Logout
    $('#logout-btn').on('click', function() {
        const token = localStorage.getItem('jwt_token');
        $.ajax({
            url: 'PHP/logout.php',
            type: 'POST',
            contentType: 'application/json',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function() {
                localStorage.removeItem('jwt_token');
                showAuth();
            },
            error: function(xhr) {
                alert('Logout failed: ' + xhr.responseJSON.message);
            }
        });
    });

    function showAuth() {
        $('#register-card').show();
        $('#login-card').show();
        $('#profile-card').hide();
    }

    function showProfile() {
        $('#register-card').hide();
        $('#login-card').hide();
        $('#profile-card').show();
    }

    function loadProfile() {
        const token = localStorage.getItem('jwt_token');
        $.ajax({
            url: 'PHP/profile.php',
            type: 'GET',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function(response) {
                $('#profile-bio').val(response.bio || '');
                $('#profile-email').val(response.email || '');
                $('#display-username').text(response.username || '');
                $('#display-timestamp').text(response.updated_at || '');
            },
            error: function(xhr) {
                alert('Failed to load profile: ' + xhr.responseJSON.message);
            }
        });
    }
});