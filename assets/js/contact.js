// assets/js/contact.js - State -> City dropdown for contact.php
document.addEventListener('DOMContentLoaded', function () {
    var stateSelect = document.getElementById('state');
    var citySelect  = document.getElementById('city');
    if (!stateSelect || !citySelect) return;

    var citiesByState = {
        "Andhra Pradesh": ["Visakhapatnam","Vijayawada","Guntur","Nellore","Kurnool","Rajahmundry","Tirupati","Kakinada","Kadapa","Anantapur"],
        "Arunachal Pradesh": ["Itanagar","Naharlagun","Pasighat","Tawang","Bomdila","Ziro","Along","Tezu"],
        "Assam": ["Guwahati","Silchar","Dibrugarh","Jorhat","Nagaon","Tinsukia","Tezpur","Sivasagar","Dhubri","Diphu"],
        "Bihar": ["Patna","Gaya","Bhagalpur","Muzaffarpur","Purnia","Darbhanga","Bihar Sharif","Arrah","Begusarai","Katihar"],
        "Chhattisgarh": ["Raipur","Bhilai","Durg","Bilaspur","Korba","Rajnandgaon","Jagdalpur","Ambikapur"],
        "Goa": ["Panaji","Vasco da Gama","Margao","Mapusa","Ponda"],
        "Gujarat": ["Ahmedabad","Surat","Vadodara","Rajkot","Bhavnagar","Jamnagar","Junagadh","Gandhinagar","Anand","Bharuch","Vapi","Navsari","Morbi"],
        "Haryana": ["Gurugram","Faridabad","Panipat","Ambala","Hisar","Karnal","Rohtak","Sonipat","Yamunanagar","Panchkula","Kurukshetra","Rewari"],
        "Himachal Pradesh": ["Shimla","Dharamshala","Solan","Mandi","Kullu","Manali","Hamirpur","Bilaspur","Una","Chamba"],
        "Jharkhand": ["Ranchi","Jamshedpur","Dhanbad","Bokaro","Deoghar","Hazaribagh","Giridih","Ramgarh","Medininagar","Chaibasa"],
        "Karnataka": ["Bengaluru","Mysuru","Mangaluru","Hubballi","Dharwad","Belagavi","Kalaburagi","Davangere","Ballari","Shivamogga","Tumakuru","Udupi","Hassan","Raichur"],
        "Kerala": ["Thiruvananthapuram","Kochi","Kozhikode","Kollam","Thrissur","Kannur","Alappuzha","Palakkad","Kottayam","Malappuram","Kasaragod"],
        "Madhya Pradesh": ["Bhopal","Indore","Jabalpur","Gwalior","Ujjain","Sagar","Dewas","Satna","Ratlam","Rewa","Burhanpur","Singrauli"],
        "Maharashtra": ["Mumbai","Pune","Nagpur","Nashik","Thane","Aurangabad","Navi Mumbai","Solapur","Kolhapur","Amravati","Sangli","Jalgaon","Akola","Latur","Ahmednagar"],
        "Manipur": ["Imphal","Thoubal","Bishnupur","Churachandpur","Ukhrul","Senapati"],
        "Meghalaya": ["Shillong","Tura","Jowai","Nongpoh","Williamnagar","Baghmara"],
        "Mizoram": ["Aizawl","Lunglei","Champhai","Kolasib","Serchhip","Saiha"],
        "Nagaland": ["Kohima","Dimapur","Mokokchung","Tuensang","Wokha","Mon"],
        "Odisha": ["Bhubaneswar","Cuttack","Rourkela","Berhampur","Sambalpur","Puri","Balasore","Baripada","Jharsuguda","Bargarh","Angul"],
        "Punjab": ["Ludhiana","Amritsar","Jalandhar","Patiala","Bathinda","Mohali","Pathankot","Hoshiarpur","Moga","Firozpur","Batala"],
        "Rajasthan": ["Jaipur","Jodhpur","Udaipur","Kota","Ajmer","Bikaner","Alwar","Bharatpur","Sikar","Bhilwara","Sri Ganganagar","Pali","Barmer"],
        "Sikkim": ["Gangtok","Namchi","Gyalshing","Mangan","Ravangla"],
        "Tamil Nadu": ["Chennai","Coimbatore","Madurai","Tiruchirappalli","Salem","Tiruppur","Erode","Vellore","Thoothukudi","Tirunelveli","Thanjavur","Dindigul","Hosur","Nagercoil","Kanchipuram"],
        "Telangana": ["Hyderabad","Warangal","Nizamabad","Karimnagar","Khammam","Ramagundam","Mahbubnagar","Nalgonda","Adilabad","Siddipet"],
        "Tripura": ["Agartala","Dharmanagar","Udaipur","Kailashahar","Belonia","Ambassa"],
        "Uttar Pradesh": ["Lucknow","Kanpur","Ghaziabad","Agra","Varanasi","Prayagraj","Meerut","Noida","Greater Noida","Bareilly","Aligarh","Moradabad","Saharanpur","Gorakhpur","Mathura","Firozabad","Ayodhya","Jhansi","Muzaffarnagar","Rampur"],
        "Uttarakhand": ["Dehradun","Haridwar","Haldwani","Rishikesh","Roorkee","Nainital","Rudrapur","Kashipur","Almora","Mussoorie","Pithoragarh"],
        "West Bengal": ["Kolkata","Howrah","Durgapur","Asansol","Siliguri","Kharagpur","Bardhaman","Malda","Baharampur","Haldia","Darjeeling","Jalpaiguri","Raiganj","Cooch Behar","Krishnanagar","Suri","Balurghat"],
        "Andaman and Nicobar Islands": ["Port Blair","Diglipur","Mayabunder","Rangat"],
        "Chandigarh": ["Chandigarh"],
        "Dadra and Nagar Haveli and Daman and Diu": ["Daman","Diu","Silvassa"],
        "Delhi": ["New Delhi","Delhi","Dwarka","Rohini","Saket","Karol Bagh","Lajpat Nagar","Janakpuri","Vasant Kunj"],
        "Jammu and Kashmir": ["Srinagar","Jammu","Anantnag","Baramulla","Kathua","Udhampur","Sopore","Rajouri"],
        "Ladakh": ["Leh","Kargil","Nubra","Diskit"],
        "Lakshadweep": ["Kavaratti","Agatti","Amini","Andrott","Minicoy"],
        "Puducherry": ["Puducherry","Karaikal","Mahe","Yanam"]
    };

    function populateCities(state, selectedCity) {
        citySelect.innerHTML = '<option value="">-- Select City --</option>';
        if (!state || !citiesByState[state]) {
            citySelect.innerHTML = '<option value="">-- Select State First --</option>';
            citySelect.disabled = true;
            return;
        }
        citySelect.disabled = false;
        citiesByState[state].concat(['Other']).forEach(function (city) {
            var opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            if (city === selectedCity) opt.selected = true;
            citySelect.appendChild(opt);
        });
    }

    stateSelect.addEventListener('change', function () { populateCities(this.value, ''); });

    // Restore values after a validation error (server passes them via data attributes)
    var currentState = stateSelect.dataset.selected || stateSelect.value;
    if (currentState) {
        stateSelect.value = currentState;
        populateCities(currentState, citySelect.dataset.selected || '');
    }
});