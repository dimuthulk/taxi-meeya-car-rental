<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Feedback - TaxiMEEYA</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"
    rel="stylesheet"
  />
    <link rel="stylesheet" href="css/style1.css" />
  <link rel="stylesheet" href="css/Contactstyles.css" />

</head>
<body>
  <!-- HEADER SECTION -->
          <header class="header">
            <a href="#" id="logo"><img src="./images/Taxi_Meeya.png" alt=""></a>
            <nav class="navbar">
                <a href="index.html">Home</a>
                <a href="#">Ride</a>
                <a href="#">Our Rides</a>
                <a href="contact-us.php">Contact Us</a>
                <a href="#">About Us</a>
            </nav>
            
            <!-- User Profile Section -->
            <div class="user-profile" id="user-profile">
                <div class="profile-info">
                   <span id="user-welcome" class="user-welcome">
                </div>
                <div class="profile-avatar" id="profile-avatar">
                    <img src="./images/jhon.png" alt="User Avatar" class="avatar-img">
                    <div class="online-status"></div>
                </div>
                <div class="profile-dropdown" id="profile-dropdown">
                    <!-- <a href="#" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-history"></i>
                        <span>Booking History</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a> -->
                    <hr class="dropdown-divider">
                    <a href="login.html" class="dropdown-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
            
            <a href="#" id="menu-bars" class="fas fa-bars" title="Open Menu" aria-label="Open Menu"></a>
        </header>

  <!-- FEEDBACK SECTION -->
  <section class="contact-section">
    <div class="container">
      <h2>Feedback Form</h2>

      <form
        class="contact-form"
        name="feedbackForm"
        action="php/submit_feedback.php"
        method="POST"
        onsubmit="return validateForm();"
      >
        <input type="text" name="name" placeholder="Your Full Name" />
        <input type="email" name="email" placeholder="Your Email" />
        <input type="text" name="nic" placeholder="Your NIC Number" />

        <div
          class="rating"
          aria-label="Rate our service from 0 to 5 stars"
          role="radiogroup"
        >
          <input type="radio" id="star5" name="rating" value="5" />
          <label for="star5" title="5 stars">&#9733;</label>
          <input type="radio" id="star4" name="rating" value="4" />
          <label for="star4" title="4 stars">&#9733;</label>
          <input type="radio" id="star3" name="rating" value="3" />
          <label for="star3" title="3 stars">&#9733;</label>
          <input type="radio" id="star2" name="rating" value="2" />
          <label for="star2" title="2 stars">&#9733;</label>
          <input type="radio" id="star1" name="rating" value="1" />
          <label for="star1" title="1 star">&#9733;</label>
          <input
            type="radio"
            id="star0"
            name="rating"
            value="0"
            checked
            style="display: none"
          />
        </div>

        <textarea name="comments" placeholder="Your Feedback"></textarea>
        <button type="submit">Submit Feedback</button>
      </form>

      <div class="contact-info">
        <h3>Get in Touch</h3>
        <p><i class="fas fa-phone"></i> +94 70 123 4567</p>
        <p><i class="fas fa-envelope"></i> support@taximeeya.lk</p>
        <p><i class="fas fa-map-marker-alt"></i> 123 Main Street, Colombo, Sri Lanka</p>
        <p><i class="fas fa-clock"></i> Mon – Sun: 6.00am – 11.00pm</p>
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63341.17557687944!2d79.83800584719576!3d6.921839495399819!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae25925a5c9766d%3A0xc1b64608b74b0c78!2sColombo!5e0!3m2!1sen!2slk!4v1665083781234!5m2!1sen!2slk"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
      </div>
    </div>
  </section>

    <script src="js/script.js"></script>
    <script src="js/auth.js"></script>
  <script>
    function validateForm() {
      var form = document.forms["feedbackForm"];
      var name = form["name"].value.trim();
      var email = form["email"].value.trim();
      var nic = form["nic"].value.trim();
      var comments = form["comments"].value.trim();

      // Validate rating radio buttons
      var ratingChecked = false;
      var ratings = form["rating"];
      if (ratings.length) {
        for (var i = 0; i < ratings.length; i++) {
          if (ratings[i].checked) {
            ratingChecked = true;
            break;
          }
        }
      } else {
        ratingChecked = ratings.checked;
      }

      if (!name || !email || !nic || !ratingChecked || !comments) {
        alert("All fields must be filled out.");
        return false;
      }

      var emailPattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
      if (!emailPattern.test(email)) {
        alert("You have entered an invalid email address!");
        return false;
      }

      if (!/^\d{9}[vVxX]$|^\d{12}$/.test(nic)) {
        alert("NIC must be 9 digits followed by 'V' or 12 digits (new format).");
        return false;
      }

      return true;
    }
  </script>
</body>
</html>
