<x-app-layout>
    @section("title", "FAQ - BlueSky Logistics")

    <div class="bg-gray-100 min-h-screen py-10">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-extrabold mb-10 text-gray-800 text-center tracking-tight">
                Frequently Asked Questions – BlueSky Logistics
            </h1>

            <div class="space-y-4 max-w-3xl mx-auto">
                @php
                    $faqs = [
                        ['q' => 'How can you help with my shipping needs?', 'a' => 'We offer a variety of logistics solutions depending on what you\'re shipping, where it\'s going. Whether it\'s local or international, we’ll find the best route and method for your cargo.'],
                        ['q' => 'What kinds of shipments do you usually handle?', 'a' => 'From small parcels to large shipments, we manage a wide range of shipments.'],
                        ['q' => 'Can I see where my shipment is right now?', 'a' => 'Of course. Every shipment comes with tracking info so you can check its progress anytime.'],
                        ['q' => 'How much does shipping cost with you?', 'a' => 'Rates vary depending on size and weight.'],
                        ['q' => 'What if something goes wrong with my delivery?', 'a' => 'If your shipment is delayed or something seems off, contact us right away. We’ll investigate and work to resolve the issue as quickly as possible.'],
                        ['q' => 'How can I contact customer service?', 'a' => 'You can reach us through:<br><strong>Phone:</strong>+32 2 456 78 90<br><strong>Email:</strong> support@bluesky.com'],
                        
                    ];
                @endphp

                @foreach($faqs as $index => $faq)
                    <div class="bg-white rounded-xl shadow-md p-5">
                        <button 
                            class="w-full text-left flex justify-between items-center text-gray-800 font-semibold text-lg faq-toggle focus:outline-none"
                            data-index="{{ $index }}"
                        >
                            {{ $faq['q'] }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="mt-3 text-gray-600 hidden faq-answer" id="faq-answer-{{ $index }}">
                            {!! $faq['a'] !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.faq-toggle').forEach(button => {
            button.addEventListener('click', function () {
                const index = this.dataset.index;
                const answer = document.getElementById(`faq-answer-${index}`);
                const icon = this.querySelector('svg');

                answer.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            });
        });
    </script>
</x-app-layout>
