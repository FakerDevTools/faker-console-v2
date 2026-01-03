<?php

$stmt = $mysqli->prepare('
    SELECT id, plan
    FROM users 
    WHERE id = ?
    LIMIT 1
');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($user_id, $plan);
$stmt->fetch();
$stmt->close();

if(isset($_GET['id'])) {

    $plan = (int) $_GET['id'];

    if (in_array($plan, [1,2,3,4])) {

        $stmt = $mysqli->prepare('
            UPDATE users SET 
            plan = ?,
            updated_at = NOW()
            WHERE id = ?
        ');
        $stmt->bind_param('ii', $plan, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();

        message_set('Plan has been updated.', 'success');
        header_redirect('/plan');

    }
    else 
    {

        message_set('Invalid plan selected.', 'error');
        header_redirect('/plan');

    }

}

define('MENU', 'plan');
define('TITLE', 'Plans');

include __DIR__ . '/templates/html_header.php';
include __DIR__ . '/templates/dashboard_header.php';

?>

<h1>Plans</h1>

<div class="w3-flex">

    <div class="w3-flex-item w3-leftbar w3-margin-right w3-border-<?= ($plan == 1 ? "green" : "red") ?>" style="flex: 1 1 25%; max-width: 25%;">
        <a href="#" onclick="selectPricing(1, event)" class="w3-border" style="display:flex; align-items:center; padding:8px; border: height:100%;">
            <div style="flex:1; display:flex; flex-direction:column; justify-content:center; margin-left:10px;">
                <h4 style="margin:0">
                    <input type="radio" name="plan" value="1" <?= ($plan === 1 ? 'checked' : '') ?>>
                    Free Tier
                </h4>
                <p class="w3-text-grey w3-bold">$0.00 per month</p>
                <p class="w3-text-grey" style="margin:0">Enjoy basic access to our APIs with limited features.</p>
                <ul class="w3-text-grey" style="padding-left: 20px">
                    <li>Access to all basic APIs</li>
                    <li>100 requests per month</li>
                    <li>Community support</li>
                </ul>
            </div>
        </a>
    </div>

    <div class="w3-flex-item w3-leftbar w3-margin-right w3-border-<?= ($plan == 2 ? "green" : "red") ?>" style="flex: 1 1 25%; max-width: 25%;">
        <a href="#" onclick="selectPricing(2, event)" class="w3-border" style="display:flex; align-items:center; padding:8px; border:">
            <div style="flex:1; display:flex; flex-direction:column; justify-content:center; margin-left:10px;">
                <h4 style="margin:0">
                    <input type="radio" name="plan" value="2" <?= ($plan === 2 ? 'checked' : '') ?>>
                    Basic Tier
                </h4>
                <p class="w3-text-grey w3-bold">$20.00 per month</p>
                <p class="w3-text-grey" style="margin:0">Enjoy access to our APIs with unlimited features.</p>
                <ul class="w3-text-grey" style="padding-left: 20px">
                    <li>Access to all APIs</li>
                    <li>500 requests per month</li>
                    <li>Email technical support</li>
                </ul>
            </div>
        </a>
    </div>

    <div class="w3-flex-item w3-leftbar w3-margin-right w3-border-<?= ($plan == 3 ? "green" : "red") ?>" style="flex: 1 1 25%; max-width: 25%;">
        <a href="#" onclick="selectPricing(3, event)" class="w3-border" style="display:flex; align-items:center; padding:8px; border:">
            <div style="flex:1; display:flex; flex-direction:column; justify-content:center; margin-left:10px;">
                <h4 style="margin:0">
                    <input type="radio" name="plan" value="3" <?= ($plan === 3 ? 'checked' : '') ?>>
                    Advanced Tier
                </h4>
                <p class="w3-text-grey w3-bold">$100.00 per month</p>
                <p class="w3-text-grey" style="margin:0">Enjoy access to our APIs with unlimited features.</p>
                <ul class="w3-text-grey" style="padding-left: 20px">
                    <li>Access to all APIs</li>
                    <li>5000 requests per month</li>
                    <li>Phone technical support</li>
                </ul>
            </div>
        </a>
    </div>

    <div class="w3-flex-item w3-leftbar w3-margin-right w3-border-<?= ($plan == 4 ? "green" : "red") ?>" style="flex: 1 1 25%; max-width: 25%;">
        <a href="#" onclick="selectPricing(4, event)" class="w3-border" style="display:flex; align-items:center; padding:8px; border:">
            <div style="flex:1; display:flex; flex-direction:column; justify-content:center; margin-left:10px;">
                <h4 style="margin:0">
                    <input type="radio" name="plan" value="4" <?= ($plan === 4 ? 'checked' : '') ?> disabled>
                    Custom Tier *
                </h4>
                <p class="w3-text-grey w3-bold">Custom Pricing</p>
                <p class="w3-text-grey" style="margin:0">Enjoy access to our APIs with unlimited features.</p>
                <ul class="w3-text-grey" style="padding-left: 20px">
                    <li>Access to all APIs</li>
                    <li>5000+ requests per month</li>
                    <li>Phone technical support</li>
                </ul>
            </div>
        </a>
    </div>

</div>

<div class="w3-center">

    <button class="w3-button w3-black w3-margin-top" onclick="savePricing()">
        <i class="fas fa-floppy-disk"></i> Save Plan Changes
    </button>

    <p class="w3-text-grey">* <a href="https://faker.ca/contact">Contact Us</a> for Custom Tier pricing and setup.</p>

</div>

<script>

function savePricing() {
    
    const radios = document.getElementsByName('plan');

    let selectedPlan = null;

    radios.forEach(radio => {
        if (radio.checked) {
            selectedPlan = radio.value;
        }
    });

    window.location.href = '/plan/id/' + selectedPlan;

}

function selectPricing(plan, e) {

    e.preventDefault();

    // Uncheck all radio buttons
    const radios = document.getElementsByName('plan');
    radios.forEach(radio => {
        radio.checked = false;
    });

    // Check the selected radio button
    radios[plan - 1].checked = true;

    // Switch all leftbar to red
    const plans = document.getElementsByClassName('w3-flex-item');
    for (let i = 0; i < plans.length; i++) {
        plans[i].classList.remove('w3-border-green');
        plans[i].classList.add('w3-border-red');
    }

    let selectedPlan = event.target;
    selectedPlan = selectedPlan.closest(".w3-flex-item");
    console.log(selectedPlan);

    // Set selected leftbar to green
    selectedPlan.classList.remove('w3-border-red');
    selectedPlan.classList.add('w3-border-green');  

}

</script>
    
<?php 

include __DIR__ . '/templates/dashboard_footer.php';
include __DIR__ . '/templates/html_footer.php';

