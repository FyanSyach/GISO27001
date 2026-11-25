import { useId } from 'react';

/**
 * Logo perisai GISO27001.
 * Dekoratif, sehingga nama aplikasi tetap ditulis terpisah agar terbaca pembaca layar.
 * Gradient memakai ID unik agar beberapa instance pada satu halaman tidak saling menimpa.
 */
export default function BrandLogo({ className = 'h-10 w-10' }: { className?: string }) {
    const gradientId = `giso-shield-${useId().replace(/:/g, '')}`;

    return (
        <svg className={className} viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">
            <defs>
                <linearGradient id={gradientId} x1="8" y1="4" x2="40" y2="44" gradientUnits="userSpaceOnUse">
                    <stop stopColor="#38BDF8" />
                    <stop offset="0.55" stopColor="#2563EB" />
                    <stop offset="1" stopColor="#10B981" />
                </linearGradient>
            </defs>
            <path
                d="M24 3.5 42 10v13.2c0 10.6-7.4 19.2-18 21.3C13.4 42.4 6 33.8 6 23.2V10L24 3.5Z"
                fill={`url(#${gradientId})`}
            />
            <path
                d="M24 8.4 37.6 13.3v9.9c0 8-5.5 14.6-13.6 16.4C15.9 37.8 10.4 31.2 10.4 23.2v-9.9L24 8.4Z"
                fill="#0B1220"
                fillOpacity="0.28"
            />
            <path
                d="m16.8 23.6 5 5 9.6-10.4"
                stroke="#F8FAFC"
                strokeWidth="3.6"
                strokeLinecap="round"
                strokeLinejoin="round"
            />
        </svg>
    );
}
