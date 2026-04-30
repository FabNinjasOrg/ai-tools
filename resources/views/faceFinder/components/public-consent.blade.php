<div id="ff-public-consent-overlay" class="hidden fixed inset-0 z-[998] bg-slate-900/40 backdrop-blur-sm"></div>

<div
    id="ff-public-consent"
    class="hidden fixed bottom-4 left-1/2 -translate-x-1/2 z-[999] w-[95%] max-w-3xl rounded-2xl border border-slate-200 bg-white/95 shadow-xl backdrop-blur px-5 py-4 flex flex-col gap-3 md:flex-row md:items-center"
>
    <div class="flex-1 text-sm text-slate-700">
        <p class="font-semibold text-slate-900">Cookies & face matching consent</p>
        <p class="text-xs text-slate-500 mt-1">This website uses cookies. By continuing to browse the site, you are agreeing to our use of essential cookies and use of biometrics if involved.</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <a
            href="{{ route('face_finder.privacy_policy') }}"
            target="_blank"
            class="px-4 py-2 rounded-xl border border-transparent text-xs font-semibold text-slate-500 hover:text-slate-900 underline"
        >
            Privacy Policy
        </a>
        <button
            type="button"
            data-ff-consent="reject"
            class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-600 hover:text-slate-900 hover:border-slate-400"
        >
            Reject
        </button>
        <button
            type="button"
            data-ff-consent="accept"
            class="px-4 py-2 rounded-xl bg-emerald-600 text-xs font-semibold text-white shadow hover:bg-emerald-700"
        >
            Accept
        </button>
    </div>
</div>

<script>
    (() => {
        const banner = document.getElementById('ff-public-consent');
        const overlay = document.getElementById('ff-public-consent-overlay');

        if (!banner) return;

        const isPublicPage = document.body?.dataset?.publicPage === 'true';
        const hasOtpSession = document.body?.dataset?.hasOtpSession === 'true';
        const COOKIE_NAME = isPublicPage ? 'ff_public_consent' : 'ff_app_consent';

        const isLoggedIn = @json(auth()->check());
        const saveConsentUrl = @json(route('face_finder.save_consent'));
        const saveGuestConsentUrl = @json(route('face_finder.public.save_guest_consent'));

        function setCookie(name, value, days = 365) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
        }

        function getCookie(name) {
            return document.cookie
                .split('; ')
                .find(row => row.startsWith(name + '='))?.split('=')[1] || null;
        }

        const consentValue = getCookie(COOKIE_NAME);

        if (!consentValue) {
            if (!isPublicPage || (isPublicPage && hasOtpSession)) {
                banner.classList.remove('hidden');
            }
        }

        const handleDecision = async (decision) => {
            setCookie(COOKIE_NAME, decision, 365);

            if (isLoggedIn && !isPublicPage) {
                try {
                    const response = await fetch(saveConsentUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ status: decision })
                    });
                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        console.error('Failed to save consent:', data.message || 'Unknown error');
                    }
                } catch (error) {
                    console.error('Error saving consent:', error);
                }
            } else if (isPublicPage) {
                try {
                    const response = await fetch(saveGuestConsentUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ status: decision })
                    });
                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        console.error('Failed to save guest consent:', data.message || 'Unknown error');
                    }
                } catch (error) {
                    console.error('Error saving guest consent:', error);
                }
            }

            banner.classList.add('hidden');
            overlay?.classList.add('hidden');
            document.documentElement.classList.remove('overflow-hidden');
        };

        banner.querySelectorAll('[data-ff-consent]').forEach((btn) => {
            btn.addEventListener('click', () => handleDecision(btn.dataset.ffConsent));
        });

        const requireConsentForUploads = (event) => {
            const trigger = event.target.closest('[data-force-consent]');
            if (!trigger) {
                return;
            }

            const decision = getCookie(COOKIE_NAME);
            if (decision === 'accept') {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();
            banner.classList.remove('hidden');
            overlay?.classList.remove('hidden');
            document.documentElement.classList.add('overflow-hidden');
        };

        document.addEventListener('click', requireConsentForUploads, true);
    })();
</script>
