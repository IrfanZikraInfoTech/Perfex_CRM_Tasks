<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Form</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-layer-1 {
            z-index: -2;
        }
        .bg-layer-2 {
            z-index: -4;
        }
        .noscroll::-webkit-scrollbar {
        display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .noscroll {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
        }
    </style>
    
</head>
<body class="relative overflow-hidden bg-slate-900">

    <div class="bg-layer-1 absolute w-full h-full text-[100px] text-red-700 p-20 parallax" str="20">	&#60;&#47;&#62;</div>
    <div class="bg-layer-2 absolute w-full h-full text-[82px] text-green-800 parallax px-80 py-20 flex items-end justify-end" str="-12">###</div>

    <div class="min-h-screen flex flex-col items-center justify-center parallax" str="10" >
        

        <form class="bg-white mt-[50px] p-8 border-4 relative border-double border-yellow-500 rounded-3xl shadow-lg shadow-white w-full max-w-4xl parallax" str="-30">

            <div style="position: absolute;top: -20%;left: 50%;transform: translate(-50%, 0);"><img src="https://i.ibb.co/RhSFc27/Zikra-Infotec-for-web-png.png" class="h-52 mx-auto" /></div>

            <h2 class="text-2xl mb-4 text-center"></h2>
            <div class="overflow-y-scroll noscroll max-h-[70vh]">
                <div class="mb-4">
                    <label for="name" class="block mb-2">Name</label>
                    <input id="name" class="w-full p-2 border border-gray-300 rounded" type="text" placeholder="Your Name">
                </div>
                <div class="mb-4">
                    <label for="email" class="block mb-2">Email</label>
                    <input id="email" class="w-full p-2 border border-gray-300 rounded" type="email" placeholder="Your Email">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block mb-2">Phone</label>
                    <input id="phone" class="w-full p-2 border border-gray-300 rounded" type="tel" placeholder="Your Phone">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block mb-2">Phone</label>
                    <input id="phone" class="w-full p-2 border border-gray-300 rounded" type="tel" placeholder="Your Phone">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block mb-2">Phone</label>
                    <input id="phone" class="w-full p-2 border border-gray-300 rounded" type="tel" placeholder="Your Phone">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block mb-2">Phone</label>
                    <input id="phone" class="w-full p-2 border border-gray-300 rounded" type="tel" placeholder="Your Phone">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block mb-2">Phone</label>
                    <input id="phone" class="w-full p-2 border border-gray-300 rounded" type="tel" placeholder="Your Phone">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block mb-2">Phone</label>
                    <input id="phone" class="w-full p-2 border border-gray-300 rounded" type="tel" placeholder="Your Phone">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block mb-2">Phone</label>
                    <input id="phone" class="w-full p-2 border border-gray-300 rounded" type="tel" placeholder="Your Phone">
                </div>
                <button class="bg-sky-500 hover:bg-sky-700  transition-all duration-150 ease-linear shadow outline-none bg-pink-500 hover:bg-pink-600 hover:shadow-lg focus:outline-none text-white py-3 px-4 rounded-lg text-lg w-full" type="submit">Submit</button>
            </div>
        </form>

    </div>

    <script>
        document.addEventListener('mousemove', function(e) {

        const parallaxItems = document.querySelectorAll('.parallax');

        parallaxItems.forEach(element => {
            const str = element.getAttribute("str");
            const xPos = (window.innerWidth / 2 - e.clientX) / str;
            const yPos = (window.innerHeight / 2 - e.clientY) / str;
            element.style.transform = `translate(${xPos}px, ${yPos}px)`;
        });
        

        });
    </script>

</body>
</html>