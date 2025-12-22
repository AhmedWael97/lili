@extends('layouts.app')

@section('title', 'Brand Setup')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50 py-8">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="bg-white rounded-xl shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Brand Profile Setup</h1>
            
            <form id="brandForm" onsubmit="saveBrand(event)">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Brand Name *</label>
                        <input type="text" name="name" value="{{ $brand->name ?? '' }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <input type="url" name="website" value="{{ $brand->website ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Industry *</label>
                        <input type="text" name="industry" value="{{ $brand->industry ?? '' }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="e.g., E-commerce, SaaS, Fashion">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                        <select name="country" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="US" {{ ($brand->country ?? '') == 'US' ? 'selected' : '' }}>United States</option>
                            <option value="GB" {{ ($brand->country ?? '') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="CA" {{ ($brand->country ?? '') == 'CA' ? 'selected' : '' }}>Canada</option>
                            <option value="AU" {{ ($brand->country ?? '') == 'AU' ? 'selected' : '' }}>Australia</option>
                            <option value="DE" {{ ($brand->country ?? '') == 'DE' ? 'selected' : '' }}>Germany</option>
                            <option value="FR" {{ ($brand->country ?? '') == 'FR' ? 'selected' : '' }}>France</option>
                            <option value="AE" {{ ($brand->country ?? '') == 'AE' ? 'selected' : '' }}>UAE</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Budget ($)</label>
                        <input type="number" name="monthly_budget" value="{{ $brand->monthly_budget ?? '' }}" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="USD" {{ ($brand->currency ?? 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="EUR" {{ ($brand->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="GBP" {{ ($brand->currency ?? '') == 'GBP' ? 'selected' : '' }}>GBP</option>
                            <option value="AED" {{ ($brand->currency ?? '') == 'AED' ? 'selected' : '' }}>AED</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Brand Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">{{ $brand->description ?? '' }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex space-x-4">
                    <button type="submit" class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                        Save Brand Profile
                    </button>
                    <a href="{{ route('marketing.os.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
async function saveBrand(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('{{ route("marketing.os.store-brand") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            alert('Brand profile saved successfully!');
            window.location.href = '{{ route("marketing.os.index") }}';
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error saving brand: ' + error.message);
    }
}
</script>
@endsection
