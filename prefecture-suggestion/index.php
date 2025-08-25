<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
    <div class="container">
        <h2>Several Questions to know what Japanese prefecture you might like</h2>

        <p>Please answer these questions below</p>

        <form id="questionForm">
            <div class="form-group">
                <label for="question0">Where are you from?</label>
                <select id="question0" name="country" required>
                    <option value="">Select your country</option>
                </select>
            </div>

            <div class="form-group">    
                <label for="question1">What is your favorite type of cuisine in your country?</label>
                
                <input type="text" id="question2" name="cuisine" required>
            </div>
            
            <div class="form-group">        
                <label for="question2">What is your preferred climate(Celcius degree) in your country?</label>
                <input type="text" id="question3" name="climate" required>
            </div>

            // some buttons to answer the question
            <div class="form-group">
                
                <label for="question3">What is your favorite season in your country?</label>
                    <input type="button" id="question3" name="season" value="Spring" required>
                    <input type="button" id="question3" name="season" value="Summer" required>
                    <input type="button" id="question3" name="season" value="Fall" required>
                    <input type="button" id="question3" name="season" value="Winter" required>
            </div>

            <button type="button" class="btn-register" onclick="register()">Done</button>
        </form>
        <div id="message"></div>
    </div>

    <script>
        const script = document.createElement('script');
        script.src = 'country_list.js';
        document.head.appendChild(script);
    </script>

</html>
