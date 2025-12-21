/**
 * Content Generator Module
 * Handles AI-powered content generation UI interactions
 */

class ContentGenerator {
    constructor() {
        this.form = document.getElementById('content-generation-form');
        this.previewArea = document.getElementById('content-preview');
        this.generateBtn = document.getElementById('generate-btn');
        this.isGenerating = false;

        if (this.form) {
            this.init();
        }
    }

    init() {
        this.form.addEventListener('submit', (e) => this.handleGenerate(e));
        
        // Real-time character counter
        const captionInput = document.getElementById('caption-input');
        if (captionInput) {
            captionInput.addEventListener('input', () => this.updateCharCount());
        }
    }

    async handleGenerate(e) {
        e.preventDefault();

        if (this.isGenerating) return;

        this.isGenerating = true;
        this.showLoading();

        const formData = new FormData(this.form);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('/content/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.displayPreview(result.preview);
                this.showSuccess('Content generated successfully!');
            } else {
                this.showError(result.error || 'Failed to generate content');
            }

        } catch (error) {
            this.showError('An error occurred. Please try again.');
            console.error('Generation error:', error);
        } finally {
            this.isGenerating = false;
            this.hideLoading();
        }
    }

    displayPreview(content) {
        if (!this.previewArea) return;

        const html = `
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Generated Content</h3>
                
                ${content.image_url ? `
                    <img src="${content.image_url}" alt="Generated" class="w-full rounded-lg mb-4">
                ` : ''}
                
                <p class="text-gray-800 mb-4 whitespace-pre-wrap">${content.caption}</p>
                
                ${content.hashtags?.length ? `
                    <div class="flex flex-wrap gap-2 mb-4">
                        ${content.hashtags.map(tag => `
                            <span class="text-blue-600 text-sm">#${tag}</span>
                        `).join('')}
                    </div>
                ` : ''}
                
                ${content.cta ? `
                    <div class="bg-blue-50 rounded p-3 mb-4">
                        <strong class="text-blue-900">CTA:</strong>
                        <span class="text-blue-700">${content.cta}</span>
                    </div>
                ` : ''}
                
                <div class="flex gap-4 mt-6">
                    <button onclick="saveAsDraft()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Save as Draft
                    </button>
                    <button onclick="scheduleContent()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Schedule
                    </button>
                    <button onclick="publishNow()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Publish Now
                    </button>
                </div>
            </div>
        `;

        this.previewArea.innerHTML = html;
        this.previewArea.classList.remove('hidden');
    }

    updateCharCount() {
        const input = document.getElementById('caption-input');
        const counter = document.getElementById('char-counter');
        
        if (input && counter) {
            const count = input.value.length;
            const max = 2200;
            counter.textContent = `${count} / ${max}`;
            counter.className = count > max ? 'text-red-600' : 'text-gray-500';
        }
    }

    showLoading() {
        if (this.generateBtn) {
            this.generateBtn.disabled = true;
            this.generateBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 mr-2 inline" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Generating...
            `;
        }
    }

    hideLoading() {
        if (this.generateBtn) {
            this.generateBtn.disabled = false;
            this.generateBtn.innerHTML = 'Generate Content';
        }
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        } text-white`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ContentGenerator();
});

// Global helper functions
window.saveAsDraft = function() {
    alert('Save as draft functionality - integrate with backend');
};

window.scheduleContent = function() {
    alert('Schedule modal - integrate with backend');
};

window.publishNow = function() {
    if (confirm('Publish this content immediately?')) {
        alert('Publish functionality - integrate with backend');
    }
};
