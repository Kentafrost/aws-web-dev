// Load countries from REST Countries API
// This script is for Node.js - to run: node country_list.js

async function loadCountries() {
    try {
        // Check if fetch is available (Node.js 18+) or use a polyfill
        if (typeof fetch === 'undefined') {
            console.log('Fetch not available. Please use Node.js 18+ or install node-fetch package.');
            return;
        }
        
        const response = await fetch('https://restcountries.com/v3.1/all?fields=name');
        console.log('Response status:', response.status);

        const countries = await response.json();
        console.log('Countries loaded:', countries.length);
        
        // Sort countries alphabetically
        countries.sort((a, b) => a.name.common.localeCompare(b.name.common));
        
        // Log all countries (since we can't use DOM in Node.js)
        console.log('\nCountries list:');
        countries.forEach((country, index) => {
            console.log(`${index + 1}. ${country.name.common}`);
        });
        
        // Save to JSON file for use in browser
        const fs = require('fs');
        const countriesData = countries.map(country => ({
            value: country.name.common,
            text: country.name.common
        }));
        
        fs.writeFileSync('countries.json', JSON.stringify(countriesData, null, 2));
        console.log('\nCountries saved to countries.json file!');
        
    } catch (error) {
        console.error('Error loading countries:', error);
        // Fallback to manual list if API fails
        addFallbackCountries();
    }
    return countries;
}