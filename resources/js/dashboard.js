/**
 * Dashboard Module
 * Handles dashboard interactions and live updates
 */

class Dashboard {
    constructor() {
        this.init();
    }

    init() {
        this.loadUsageStats();
        this.setupRefreshInterval();
    }

    async loadUsageStats() {
        try {
            const response = await fetch('/api/usage', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateUsageDisplay(data.usage);
            }
        } catch (error) {
            console.error('Failed to load usage stats:', error);
        }
    }

    updateUsageDisplay(usage) {
        // Update posts usage
        this.updateProgressBar('posts-progress', usage.posts.percentage);
        this.updateText('posts-used', usage.posts.used);
        this.updateText('posts-limit', usage.posts.limit === -1 ? '∞' : usage.posts.limit);

        // Update replies usage
        this.updateProgressBar('replies-progress', usage.comment_replies.percentage);
        this.updateText('replies-used', usage.comment_replies.used);
        this.updateText('replies-limit', usage.comment_replies.limit === -1 ? '∞' : usage.comment_replies.limit);

        // Update messages usage
        this.updateProgressBar('messages-progress', usage.dm_responses.percentage);
        this.updateText('messages-used', usage.dm_responses.used);
        this.updateText('messages-limit', usage.dm_responses.limit === -1 ? '∞' : usage.dm_responses.limit);
    }

    updateProgressBar(id, percentage) {
        const bar = document.getElementById(id);
        if (bar) {
            bar.style.width = `${percentage}%`;
            
            // Change color based on usage
            if (percentage >= 90) {
                bar.className = bar.className.replace(/bg-\w+-600/, 'bg-red-600');
            } else if (percentage >= 75) {
                bar.className = bar.className.replace(/bg-\w+-600/, 'bg-yellow-600');
            }
        }
    }

    updateText(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    }

    setupRefreshInterval() {
        // Refresh usage stats every 5 minutes
        setInterval(() => {
            this.loadUsageStats();
        }, 5 * 60 * 1000);
    }
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', () => {
    new Dashboard();
});
