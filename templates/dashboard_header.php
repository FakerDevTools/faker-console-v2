
<div class="w3-display-container" style="min-height:100vh; max-width:100%;">

    
    <div class="w3-row">
        <div class="w3-half">
            <a href="/login">
                <img src="https://cdn.faker.ca/images@1.0.0/faker-logo-coloured-horizontal.png" alt="Faker Logo" style="max-width:250px;">
            </a>
        </div>
        <div class="w3-half w3-right-align w3-padding-top" style="padding-top: 20px;">
            <a href="/account"><i class="fas fa-user-circle fa-lg w3-right-align"></i></a>
            <a href="/logout"><i class="fas fa-sign-out-alt fa-lg w3-right-align"></i></a>
        </div>
    </div>

    <hr>

    <a href="/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a> | 
    <a href="/apis"><i class="fas fa-cogs"></i> APIs</a> | 
    <a href="/keys"><i class="fas fa-key"></i> Keys | 
    <a href="/plan"><i class="fas fa-folder-open"></i> Plan</a>

    <hr>
    
    <?php message_get(); ?>

    <main>
        