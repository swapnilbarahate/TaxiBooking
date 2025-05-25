TaxiBooking
Taxi Booking Web Application built using PHP, MySQL, jQuery, AJAX, HTML/CSS/Bootstrap. This system allows users to search, calculate fare, and book taxis for local tours based on custom packages and real-time distance and duration data from Google Maps API.

User Registration • Allow users to register with its basic details.
User Login • Authenticate users using username (email or mobile) and password.
Taxi Search and Booking (Local Tour Type) • Allow users to search for taxis based on: ◦ Pickup Location (use Google Maps API) ◦ Drop Location (use Google Maps API) ◦ Tour Type: Only "Local" should be available. • Show available local packages for the selected route (e.g., 10 km/1 hr, 20 km/2 hrs, etc.).
Pricing Logic • Fetch distance and estimated travel time between pickup and drop using Google Maps API. • Compare the result with the selected package: ◦ If distance > package distance, calculate extra km charges (e.g., ₹X per km). ◦ If time > package hours, calculate extra hour charges (e.g., ₹Y per hour). • Display the final price accordingly.
Taxi Booking • After selecting the package and confirming the fare, allow users to book the taxi. • Save the booking details with fare breakdown.
