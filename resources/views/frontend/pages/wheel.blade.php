@extends('frontend.layouts.master')

@section('title', 'E-SHOP || PRODUCT PAGE')

@section('main-content')
    <!-- Product Style -->
    <section class="product-area shop-sidebar shop section">

        <div class="container">
            <canvas id="wheel"></canvas>
            <button id="spin-btn">Spin</button>
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKXlQ9iLTwjO7__KWDQWEdnZF75JpzNzSqNLtqypvyYSmvTHZK6Ufr90Z80lkBmFZ__1M&usqp=CAU"
                alt="spinner arrow" />
        </div>
        <div id="final-value">
            <p>Click On The Spin Button To Start</p>
        </div>

        <div class="claim-btn-container">
            <form id="claimForm" action="/wheel/claiming" method="POST" class="">
                @csrf
                <input type="hidden" name="slug" id="productSlug" value="">
                <input type="hidden" name="quant[1]" class="input-number" data-min="1" data-max="1000" value="1"
                    id="quantity">
                <input type="hidden" name="price" value="0">
                <input type="hidden" name="amount" value="0">
                <!-- Hidden input for quantity -->
                <button type="submit" id="claimBtn" style="display: none;">Claim Now</button>
            </form>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        .pagination {
            display: inline-flex;
        }

        .filter_button {
            /* height:20px; */
            text-align: center;
            background: #F7941D;
            padding: 8px 16px;
            margin-top: 10px;
            color: white;
        }

        .wrapper {
            width: 90%;
            max-width: 34.37em;
            max-height: 90vh;
            background-color: #ffffff;
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50%;
            left: 50%;
            padding: 3em;
            border-radius: 1em;
            box-shadow: 0 4em 5em rgba(27, 8, 53, 0.2);
        }

        .container {
            position: relative;
            width: 40%;
            height: 40%;
        }

        #wheel {
            max-height: inherit;
            width: inherit;
            top: 0;
            padding: 0;
        }

        @keyframes rotate {
            100% {
                transform: rotate(360deg);
            }
        }

        #spin-btn {
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50%;
            left: 50%;
            height: 26%;
            width: 26%;
            border-radius: 50%;
            cursor: pointer;
            border: 0;
            background: radial-gradient(#fdcf3b 50%, #d88a40 85%);
            color: #c66e16;
            text-transform: uppercase;
            font-size: 1.8em;
            letter-spacing: 0.1em;
            font-weight: 600;
        }

        img {
            position: absolute;
            width: 4em;
            top: 45%;
            right: -8%;
        }

        #final-value {
            font-size: 1.5em;
            text-align: center;
            margin-top: 1.5em;
            color: #202020;
            font-weight: 500;
        }

        @media screen and (max-width: 768px) {
            .wrapper {
                font-size: 12px;
            }

            img {
                right: -5%;
            }
        }

        .claim-btn-container {
            text-align: center;
            margin-top: 20px;
            margin-left: 915px;
            /* Adjust as needed */
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.1.0/chartjs-plugin-datalabels.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const products = {!! json_encode($products) !!};
        const productData = products.data || [];
        const numProducts = productData.length;

        const user = {!! json_encode($user) !!};
        const wheelChance = user?.wheelChance || 0;

        console.log("wheelChance: ", wheelChance);


        const rotationValues = [];
        const degreeStep = 360 / (numProducts + 1); // Adding 1 for the "Try Again" option

        productData.forEach((product, index) => {
            rotationValues.push({
                minDegree: index * degreeStep,
                maxDegree: (index + 1) * degreeStep,
                value: product.title,
                image: product.photo.replace(/\\/g, ''), // Assuming photo is a comma-separated string
                slug: "{{ route('product-detail', '') }}" + "/" + product
                    .slug // Adjust as per your route structure
            });
        });

        // Adding a "Try Again" option
        rotationValues.push({
            minDegree: numProducts * degreeStep,
            maxDegree: 360,
            value: "Try Again",
            image: "",
            slug: ""
        });
        rotationValues.push({
            minDegree: numProducts * degreeStep,
            maxDegree: 360,
            value: "Try Again",
            image: "",
            slug: ""
        });

        const data = Array(numProducts + 2).fill(1); // Adding 1 for the "Try Again" option

        const wheel = document.getElementById("wheel");
        const spinBtn = document.getElementById("spin-btn");
        const finalValue = document.getElementById("final-value");

        const myChart = new Chart(wheel, {
            plugins: [ChartDataLabels],
            type: "pie",
            data: {
                labels: rotationValues.map(value => value.value),
                datasets: [{
                    backgroundColor: [
                        "#F7941D",
                        "#424646",
                        // Add more colors if needed
                    ],


                    data: data,
                }, ],
            },
            options: {
                cutout: "50%",
                responsive: true,
                animation: {
                    duration: 0
                },
                plugins: {
                    tooltip: false,
                    legend: {
                        display: false,
                    },
                    datalabels: {
                        color: "#ffffff",
                        formatter: (_, context) => context.chart.data.labels[context.dataIndex],
                        font: {
                            size: 18,
                        },


                    },
                },
            },
        });


        const updateHiddenFields = (slug) => {
            document.getElementById('productSlug').value = slug;
            console.log("Product slug: ", slug);
        };

        const cartRoute = "{{ route('cart') }}";
        const valueGenerator = (angleValue) => {
            for (let i of rotationValues) {
                if (angleValue >= i.minDegree && angleValue <= i.maxDegree) {
                    if (i.value != "Try Again") {
                        console.log("You won the product: ", i.slug);
                        const slug = i.slug.replace("http://127.0.0.1:8000/product-detail/", "");
                        finalValue.innerHTML = `<p>Congrats, you won the product: ${i.value}</p>`;

                        updateHiddenFields(slug, 1); // Update the quantity dynamically if needed
                        document.getElementById("claimBtn").style.display = "block"; // or "inline"


                    } else {
                        finalValue.innerHTML = `<p>Please try again. Maybe next time you will win a product.</p>`;
                    }
                    spinBtn.disabled = false;
                    break;
                }
            }
        };


        let count = 0;
        let resultValue = 101;
        if (!user) {
            // If the user is a guest, display the message
            finalValue.innerHTML = `<p>Please login to spin the wheel!</p>`;
            spinBtn.disabled = true;
        } else {
            // If the user is authenticated, proceed with the wheel logic
            if (wheelChance == 0) {
                finalValue.innerHTML = `<p>You don't have a wheel point to spin!<br> Please buy point or use coupon</p>`;
                spinBtn.disabled = false;
            } else {
                const spinWheel = () => {
                    spinBtn.disabled = true;
                    const perSpin = user.wheelChance - 1;

                    axios.post('/wheel/perSpin', {
                            wheelChance: perSpin
                        }, {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Assuming you are using Laravel CSRF protection
                            }
                        })
                        .then(response => {
                            spinBtn.removeEventListener("click",
                                spinWheel); // Remove event listener after successful spin

                            finalValue.innerHTML = `<p>Good Luck!</p>`;
                            let randomDegree = Math.floor(Math.random() * (355 - 0 + 1) + 0);
                            let rotationInterval = window.setInterval(() => {
                                myChart.options.rotation = myChart.options.rotation + resultValue;
                                myChart.update();
                                if (myChart.options.rotation >= 360) {
                                    count += 1;
                                    resultValue -= 5;
                                    myChart.options.rotation = 0;
                                } else if (count > 15 && myChart.options.rotation == randomDegree) {
                                    valueGenerator(randomDegree);
                                    clearInterval(rotationInterval);
                                    count = 0;
                                    resultValue = 101;
                                }
                            }, 10);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                };

                // Add event listener for spin button
                spinBtn.addEventListener("click", spinWheel);
            }
        }
    </script>
@endpush
