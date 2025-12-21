/**
 * Content Management Module
 * Handles content listing, filtering, and actions
 */

class ContentManager {
    constructor() {
        this.currentFilter = 'all';
        this.currentPage = 1;
        this.init();
    }

    init() {
        this.setupFilterButtons();
        this.loadContent();
    }

    setupFilterButtons() {
        const filterButtons = document.querySelectorAll('[data-filter]');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const filter = e.target.dataset.filter;
                this.setFilter(filter);
                
                // Update button states
                filterButtons.forEach(btn => btn.classList.remove('bg-blue-600', 'text-white'));
                filterButtons.forEach(btn => btn.classList.add('bg-gray-100', 'text-gray-700'));
                
                e.target.classList.remove('bg-gray-100', 'text-gray-700');
                e.target.classList.add('bg-blue-600', 'text-white');
            });
        });
    }

    setFilter(filter) {
        this.currentFilter = filter;
        this.currentPage = 1;
        this.loadContent();
    }

    async loadContent() {
        const container = document.getElementById('content-grid');
        if (!container) return;

        try {
            const url = `/api/content?status=${this.currentFilter !== 'all' ? this.currentFilter : ''}&page=${this.currentPage}`;
            
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.renderContent(data.data);
            }
        } catch (error) {
            console.error('Failed to load content:', error);
        }
    }

    renderContent(items) {
        const container = document.getElementById('content-grid');
        if (!container) return;

        if (items.length === 0) {
            container.innerHTML = this.getEmptyState();
            return;
        }

        const html = items.map(item => this.renderContentCard(item)).join('');
        container.innerHTML = html;

        // Setup action buttons
        this.setupActionButtons();
    }

    renderContentCard(item) {
        const statusColors = {
            draft: 'bg-yellow-100 text-yellow-800',
            scheduled: 'bg-blue-100 text-blue-800',
            published: 'bg-green-100 text-green-800',
            failed: 'bg-red-100 text-red-800'
        };

        return `
            <div class="bg-white rounded-lg shadow overflow-hidden" data-content-id="${item.id}">
                ${item.media_url ? `
                    <img src="${item.media_url}" alt="Content" class="w-full h-48 object-cover">
                ` : `
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                `}
                
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium px-2 py-1 rounded ${statusColors[item.status]}">${item.status}</span>
                        <span class="text-xs text-gray-500">${this.formatDate(item.created_at)}</span>
                    </div>
                    
                    <p class="text-sm text-gray-700 mb-4 line-clamp-3">${this.escapeHtml(item.caption)}</p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <button class="edit-btn text-blue-600 hover:text-blue-700 text-sm" data-id="${item.id}">Edit</button>
                            ${item.status === 'draft' ? `
                                <button class="schedule-btn text-green-600 hover:text-green-700 text-sm" data-id="${item.id}">Schedule</button>
                            ` : ''}
                        </div>
                        <button class="delete-btn text-red-600 hover:text-red-700" data-id="${item.id}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    setupActionButtons() {
        // Delete buttons
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                if (confirm('Delete this content?')) {
                    this.deleteContent(id);
                }
            });
        });

        // Schedule buttons
        document.querySelectorAll('.schedule-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                this.showScheduleModal(id);
            });
        });
    }

    async deleteContent(id) {
        try {
            const response = await fetch(`/api/content/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                this.showNotification('Content deleted successfully', 'success');
                this.loadContent();
            }
        } catch (error) {
            this.showNotification('Failed to delete content', 'error');
            console.error(error);
        }
    }

    showScheduleModal(id) {
        // Simple prompt for now - can be enhanced with a proper modal
        const datetime = prompt('Enter schedule date/time (YYYY-MM-DD HH:MM):');
        if (datetime) {
            this.scheduleContent(id, datetime);
        }
    }

    async scheduleContent(id, datetime) {
        try {
            const response = await fetch(`/api/content/${id}/schedule`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ scheduled_at: datetime })
            });

            if (response.ok) {
                this.showNotification('Content scheduled successfully', 'success');
                this.loadContent();
            }
        } catch (error) {
            this.showNotification('Failed to schedule content', 'error');
            console.error(error);
        }
    }

    getEmptyState() {
        return `
            <div class="col-span-full">
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="w-20 h-20 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No content yet</h3>
                    <p class="text-gray-600 mb-6">Start creating engaging posts with AI assistance</p>
                    <a href="/content/create" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700">
                        Create Your First Post
                    </a>
                </div>
            </div>
        `;
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'just now';
        if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
        return `${Math.floor(diff / 86400)}d ago`;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    new ContentManager();
});
