<?php 
// Start session, necessary for dynamic header links (e.g., showing Login/Logout)
session_start();

// Include the Navbar, Styles, and opening HTML tags
include 'includes/header.php'; 
?>

<div class="hero">
  <h1>Find Your Perfect Life Partner</h1>
  <p>Trusted Matrimony Website to Help You Begin Your Beautiful Journey</p>

  <div class="search-box p-4 rounded shadow-lg container col-md-10">
    <form class="row g-3" action="search.php" method="GET"> 
      <div class="col-md-2">
        <select class="form-select" name="looking_for">
          <option selected>Looking for</option>
          <option value="Bride">Bride</option>
          <option value="Groom">Groom</option>
        </select>
      </div>

      <div class="col-md-2">
        <select class="form-select" name="religion">
          <option selected>Religion</option>
          <option value="Islam">Islam</option>
          <option value="Hindu">Hindu</option>
          <option value="Christian">Christian</option>
        </select>
      </div>

      <div class="col-md-2">
        <select class="form-select" name="marital_status">
          <option selected>Marital_Status</option>
          <option value="Single">Single</option>
          <option value="Married">Married</option>
          <option value="Separated">Separated</option>
          <option value="Divorced">Divorced</option>
          <option value="Widowed">Widowed</option>
        </select>
      </div>

      <div class="col-md-2">
        <select class="form-select" name="location">
          <option selected>Location (Division)</option>
          <option value="Dhaka">Dhaka</option>
          <option value="Chittagong">Chittagong</option>
          <option value="Khulna">Khulna</option>
          <option value="Rajshahi">Rajshahi</option>
          <option value="Sylhet">Sylhet</option>
          <option value="Barishal">Barishal</option>
          <option value="Rangpur">Rangpur</option>
          <option value="Mymensingh">Mymensingh</option>
        </select>
      </div>
      

      <div class="col-md-4">
        <button type="submit" class="btn btn-danger w-100">Search</button>
      </div>
    </form>
  </div>
</div>

<section class="container my-5 py-4">
  <h2 class="section-title">Why Choose Us?</h2>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature-box">
        <i class="bi bi-shield-lock-fill fs-3 text-success mb-2"></i>
        <h4>✔ 100% Verified Profiles</h4>
        <p>All profiles are manually verified for safety and trust.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="feature-box">
        <i class="bi bi-search fs-3 text-info mb-2"></i>
        <h4>✔ Advanced Search</h4>
        <p>Find your match using detailed filters like religion, profession, age, etc.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="feature-box">
        <i class="bi bi-person-check-fill fs-3 text-primary mb-2"></i>
        <h4>✔ Secure & Private</h4>
        <p>Your data is kept fully confidential and secure.</p>
      </div>
    </div>
  </div>
</section>

<section class="container my-5 py-4">
  <h2 class="section-title">Success Stories</h2>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="p-4 border rounded shadow-sm bg-light">
        <i class="bi bi-quote fs-4 text-secondary me-2"></i>
        <p class="fst-italic d-inline">"We found each other on PerfectMatch and got married last year. Highly recommended! The process was simple and secure."</p>
        <p class="mt-2 text-end text-danger fw-bold">- Rahim & Aisha</p>
      </div>
    </div>

    <div class="col-md-6">
      <div class="p-4 border rounded shadow-sm bg-light">
        <i class="bi bi-quote fs-4 text-secondary me-2"></i>
        <p class="fst-italic d-inline">"Thanks to this platform, I met my soulmate. Best matrimony website in Bangladesh!"</p>
        <p class="mt-2 text-end text-danger fw-bold">- Arif & Nila</p>
      </div>
    </div>
  </div>
</section>

<?php 
// Include the Footer, JS scripts, and closing HTML tags
include 'includes/footer.php'; 
?>