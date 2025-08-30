new Vue({
el: '#app',
data: {
    countries: [],
    seasons: ['Spring', 'Summer', 'Fall', 'Winter'],
    dishes: [],
},
created() {
    fetch('countries.json')
    .then(res => res.json())
    .then(data => {
        this.countries = data; // put into countries in data
    });
},


methods: {
    fetchDishes() {
    if (!this.form.country) return;
    fetch(`get_country_dishes.php?country=${encodeURIComponent(this.form.country)}`)
        .then(res => res.json())
        .then(data => {
        this.dishes = data;
        });
    },
    submitForm() {
    // ここでPHPに送信する処理を追加
    console.log(this.form);
    this.message = 'Form submitted successfully!';
    this.messageType = 'success';
    }
}
},


// if you don't choose something, an error message is visible
methods, {
    validateForm(message) {
        this.message = message;
        this.messageType = 'error';
    },
    checkForm() {
        if (this.form.country === "") {
            this.validateForm("please select a country");
        }

        else if (this.form.country_cuisine === "") {
            this.validateForm(`please select a country cuisine. Your country: ${this.form.country}`);
        }

        else if (this.form.season === "") {
            this.validateForm("please select a season");
        }
        else if (this.form.climate === "") {
            this.validateForm("please select a climate");
        }
        else {
            this.message = "Form is valid!";
            this.messageType = 'success';
        }

    }
});
