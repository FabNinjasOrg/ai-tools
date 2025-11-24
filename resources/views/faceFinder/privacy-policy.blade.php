@extends('faceFinder.app')

@section('content')
    <div class="max-w-4xl mx-auto px-6 py-16">
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[13px] border border-emerald-200">Privacy Policy</div>
                <h1 class="mt-3 text-3xl md:text-5xl font-extrabold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">Cookie Privacy Notice</h1>
            </div>

            <div class="prose prose-slate max-w-none">
                <p class="text-slate-700 leading-relaxed mb-6">
                    This website uses cookies to ensure a seamless and optimized user experience. By continuing to browse and use this site, you are agreeing to our use of cookies. Our commitment is to respect your privacy and ensure that all cookies employed are essential for the proper functioning of the website, and we do not misuse any private data.
                </p>

                <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">What Are Cookies?</h2>
                <p class="text-slate-700 leading-relaxed mb-6">
                    Cookies are small text files that are stored on your computer or mobile device when you visit a website. They help the website recognize your device and remember information about your visit.
                </p>

                <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">Types of Cookies We Use:</h2>

                <div class="space-y-6 mb-6">
                    <div class="border-l-4 border-emerald-500 pl-4">
                        <h3 class="text-xl font-semibold text-slate-900 mb-2">Essential Cookies:</h3>
                        <p class="text-slate-700 leading-relaxed">
                            These cookies are necessary for the website to function properly. They enable core functionalities such as page navigation and access to secure areas. The website cannot function properly without these cookies.
                        </p>
                    </div>

                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="text-xl font-semibold text-slate-900 mb-2">Performance Cookies:</h3>
                        <p class="text-slate-700 leading-relaxed">
                            These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously. We use this data to improve our website's performance.
                        </p>
                    </div>

                    <div class="border-l-4 border-purple-500 pl-4">
                        <h3 class="text-xl font-semibold text-slate-900 mb-2">Functionality Cookies:</h3>
                        <p class="text-slate-700 leading-relaxed">
                            These cookies allow the website to remember choices you make and provide enhanced, more personalized features like displaying plan details in accordance with your geographical location.
                        </p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">Face Biometric Data and Comparison</h2>
                <div class="bg-slate-50 rounded-xl p-6 mb-6 border border-slate-200">
                    <p class="text-slate-700 leading-relaxed mb-4">
                        Our service utilizes advanced face recognition technology to help you find and organize photos. When you use our face comparison features:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-slate-700 mb-4">
                        <li><strong>Biometric Data Processing:</strong> We process facial biometric data extracted from the photos you upload to enable face recognition and comparison functionality. This data is converted into mathematical representations (embeddings) that are used solely for matching faces across your photos.</li>
                        <li><strong>User Control:</strong> You have full control over your photos and biometric data. All processing occurs on our secure servers, and you can delete your photos and associated data at any time through your account settings.</li>
                        <li><strong>Data Security:</strong> Face biometric data is stored securely and is never shared with third parties. We use industry-standard encryption and security measures to protect your biometric information.</li>
                        <li><strong>Purpose Limitation:</strong> Biometric data is used exclusively for the face comparison and photo organization features you request. We do not use this data for any other purposes, including advertising or analytics.</li>
                        <li><strong>Data Retention:</strong> Biometric embeddings are stored only as long as your photos remain in your account. When you delete photos, the associated biometric data is permanently removed from our systems.</li>
                        <li><strong>Comparison Functionality:</strong> When you use our face comparison feature, we compare the facial features from your uploaded reference photo against photos in your albums or events. This comparison is performed locally on our servers and results are only visible to you.</li>
                    </ul>
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">Your Consent:</h2>
                <p class="text-slate-700 leading-relaxed mb-6">
                    By using our website, you consent to the use of essential cookies. You can manage your cookie preferences through your browser settings. Please note that blocking essential cookies may impact the functionality of the website.
                </p>
                <p class="text-slate-700 leading-relaxed mb-6">
                    By uploading photos and using our face recognition features, you consent to the processing of facial biometric data as described above. You may withdraw this consent at any time by deleting your photos and account.
                </p>

                <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">Data Security:</h2>
                <p class="text-slate-700 leading-relaxed mb-6">
                    We take the security of your data seriously. We do not misuse any private data collected through cookies, and your information is treated confidentially. Most cookies used by us are removed after you log out.
                </p>
                <p class="text-slate-700 leading-relaxed mb-6">
                    Your biometric data and photos are protected with multiple layers of security, including encryption at rest and in transit, access controls, and regular security audits. We follow industry best practices to ensure your data remains secure.
                </p>

                <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">Changes to This Notice:</h2>
                <p class="text-slate-700 leading-relaxed mb-6">
                    We may update this Cookie Privacy Notice from time to time to reflect changes in our practices or for other operational, legal, or regulatory reasons. Please revisit this page regularly to stay informed about our use of cookies and biometric data processing.
                </p>

                <div class="mt-12 pt-8 border-t border-slate-200">
                    <p class="text-sm text-slate-500 text-center">
                        Last updated: {{ date('F j, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

