<x-app-layout>
    <x-slot name="header">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Calculator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex-container">
                    <div class="calculator bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="calculator-screen" id="display"></div>
                        <div class="calculator-keys">
                            <button class="all-clear" value="all-clear">AC</button>
                            <button class="operator" value="(">(</button>
                            <button class="operator" value=")">)</button>
                            <button class="operator" value="+">+</button>
                            <button value="7">7</button>
                            <button value="8">8</button>
                            <button value="9">9</button>
                            <button class="operator" value="-">-</button>
                            <button value="4">4</button>
                            <button value="5">5</button>
                            <button value="6">6</button>
                            <button class="operator" value="/">/</button>
                            <button value="1">1</button>
                            <button value="2">2</button>
                            <button value="3">3</button>
                            <button class="operator" value="*">*</button>
                            <button class="decimal" value=".">.</button>
                            <button value="0">0</button>
                            <button class="equal-sign" value="=">=</button>
                        </div>
                    </div>
                        <div class="calculator-history ml-4">
                            <div class="calculator-history mt-4">
                                <h3 class="text-lg font-semibold mb-2">Previous Calculations:</h3>
                                <ul id="history-list"></ul>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .flex-container {
            display: flex;

        }

        .calculator {
            max-width: 400px;
            margin: 0 150px;
            border-radius: 10px;
            padding: 20px;
            flex: 1;
        }

        .calculator-history {
            flex: 1;
            margin-left: 10px;

        }

        .calculator-screen {
            width: 100%;
            height: 80px;
            font-size: 2em;
            text-align: right;
            border: none;
            outline: none;
                background-color: #8a95a1;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .calculator-keys {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        button {
            width: 100%;
            height: 60px;
            font-size: 1.5em;
            border: none;
            outline: none;
            cursor: pointer;
            background-color: #4a5568;
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2d3748;
        }

        .operator {
            background-color: #0a1c2e;
        }

        .operator:hover {
            background-color: #6f91b3;
        }

        .equal-sign {
            background-color: #38a169;
        }

        .equal-sign:hover {
            background-color: #2f855a;
        }

        .all-clear {
            background-color: #ed8936;
        }

        .all-clear:hover {
            background-color: #dd6b20;
        }

        .decimal {
            background-color: #3182ce;
        }

        .decimal:hover {
            background-color: #2c5282;
        }

    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const display = document.getElementById('display');
            const keys = document.querySelector('.calculator-keys');

            keys.addEventListener('click', function (e) {
                if (e.target.matches('button')) {
                    const key = e.target;
                    const action = key.value;

                    if (action === 'all-clear') {
                        display.innerHTML = '';
                    } else if (action === '=') {
                        const formula = display.innerHTML;
                        calculate(formula);
                    } else {
                        display.innerHTML += action;
                    }
                }
            });

            function calculate(formula) {
                fetch('api/calculate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ formula: formula, userId: {{ auth()->id() }}}),
                })
                    .then(response => response.json())
                    .then(data => {
                        display.innerHTML = data.result;
                        fetchPreviousCalculations({{ auth()->id() }});
                    })
                    .catch(error => {
                        display.innerHTML = 'Error';
                        console.error('Error:', error);
                    });
            }
        });

        function fetchPreviousCalculations(userId) {
            fetch('api/getCalculations/' + userId, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
                .then(response => response.json())
                .then(data => {
                    const historyList = document.getElementById('history-list');
                    historyList.innerHTML = '';

                    data.forEach(calculation => {
                        const li = document.createElement('li');
                        li.textContent = calculation.formula + ' = ' + calculation.result;
                        historyList.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error('Error fetching previous calculations:', error);
                });
        }

        fetchPreviousCalculations({{ auth()->id() }});

    </script>
</x-app-layout>
