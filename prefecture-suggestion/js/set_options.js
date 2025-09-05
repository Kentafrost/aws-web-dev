new Vue({
    el: '#app',
    data: {
        countries: [],
        seasons: ['Spring', 'Summer', 'Fall', 'Winter'],
        dishes: [],
        form: {
            country: '',
            country_cuisine: '',
            climate: '',
            season: ''
        },
        message: '',
        messageType: ''
    },
    created() {
        grand_parent_dir = __dirname + '/../..';
        fetch(`${grand_parent_dir}/json/countries.json`)
            .then(res => res.json())
            .then(data => {
                // Countries JSON has {value: "Country", text: "Country"} structure
                this.countries = data.map(country => country.value);
            })
            .catch(error => {
                console.error('Error loading countries:', error);
            });
    },
    methods: {
        fetchDishes() {
            if (!this.form.country) return;
            fetch(`get_country_dishes.php?country=${encodeURIComponent(this.form.country)}`)
                .then(res => res.json())
                .then(data => {
                    this.dishes = data;
                })
                .catch(error => {
                    console.error('Error fetching dishes:', error);
                });
        },
        submitForm() {
            if (this.checkForm()) {
                // Send form data to PHP
                console.log('Form data:', this.form);
                
                fetch('submit_form.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.form)
                })
                .then(res => res.json())
                .then(data => {
                    this.message = 'Form submitted successfully!';
                    this.messageType = 'success';
                    console.log('Server response:', data);
                })
                .catch(error => {
                    this.message = 'Error submitting form';
                    this.messageType = 'error';
                    console.error('Error:', error);
                });
            }
        },
        validateForm(message) {
            this.message = message;
            this.messageType = 'error';
        },
        checkForm() {
            if (this.form.country === "") {
                this.validateForm("Please select a country");
                return false;
            }
            else if (this.form.country_cuisine === "") {
                this.validateForm(`Please enter your favorite cuisine. Your country: ${this.form.country}`);
                return false;
            }
            else if (this.form.season === "") {
                this.validateForm("Please select a season");
                return false;
            }
            else if (this.form.climate === "") {
                this.validateForm("Please enter your preferred climate");
                return false;
            }
            else {
                this.message = "Form is valid!";
                this.messageType = 'success';
                return true;
            }
        },
        questionForm() {
            this.submitForm();
        }
    }
});
