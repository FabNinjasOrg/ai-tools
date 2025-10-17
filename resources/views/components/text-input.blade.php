@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 bg-white text-slate-900 focus:border-emerald-400 focus:ring-emerald-400 rounded-xl shadow-sm px-4 py-3']) }}>
