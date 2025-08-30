<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Please Answer the Following Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            margin: 5px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-login {
            background-color: #007bff;
            color: white;
        }
        .btn-register {
            background-color: #28a745;
            color: white;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
        .btn-register:hover {
            background-color: #1e7e34;
        }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f1b0b7;
        }
    </style>
</head>

<body>
    <div id="app" class="container">
        <h2>Several Questions to know what Japanese prefecture you might like</h2>
        <p>Please answer these questions below</p>

        <form @submit.prevent="questionForm">
            <!-- 国選択 -->
        <div class="form-group">
            <select v-model="form.country" @change="fetchDishes" required>
                <option disabled value="">Select your country</option>
                <option v-for="country in countries" :key="country">{{ country }}</option>
            </select>
        </div>

        <!-- 好きな料理タイプ -->
        <div class="form-group">
            <label>What is your favorite type of cuisine in your country?</label>
            <input type="text" v-model="form.country_cuisine" required>
        </div>

        <!-- 気候 -->
        <div class="form-group">
            <label>What is your preferred climate (°C) in your country?</label>
            <input type="text" v-model="form.climate" required>
        </div>

        <!-- 季節選択 -->
        <div class="form-group">
            <label>What is your favorite season in your country?</label>
            <button
                v-for="season in seasons"
                :key="season"
                type="button"
                :class="{ active: form.season === season }"
                @click="form.season = season"
            >
            {{ season }}
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="js/set_options.js"></script>

</html>