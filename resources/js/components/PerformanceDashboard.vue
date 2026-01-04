<template>
  <div class="performance-dashboard">
    <h2>Algorithm Performance Dashboard</h2>
    <p class="subtitle">Track how user feedback improves data quality</p>

    <div v-if="loading" class="loading-spinner">
      <div class="spinner-border"></div>
      <p>Loading performance data...</p>
    </div>

    <div v-else class="dashboard-content">
      <!-- Overall Stats -->
      <div class="stats-grid">
        <div class="stat-card improvement">
          <div class="stat-value">{{ dashboard.overall_improvement }}%</div>
          <div class="stat-label">Overall Improvement</div>
          <div class="stat-sublabel">Last 30 days</div>
        </div>

        <div class="stat-card">
          <div class="stat-value">{{ dashboard.total_feedback_items }}</div>
          <div class="stat-label">Total Feedback Items</div>
        </div>

        <div class="stat-card">
          <div class="stat-value">{{ dashboard.feedback_last_30_days }}</div>
          <div class="stat-label">Recent Feedback</div>
          <div class="stat-sublabel">Last 30 days</div>
        </div>
      </div>

      <!-- Component Performance -->
      <div class="component-performance">
        <h3>Component Performance</h3>
        
        <div
          v-for="(performance, component) in dashboard.component_performance"
          :key="component"
          class="component-card"
        >
          <div class="component-header">
            <h4>{{ formatComponentName(component) }}</h4>
            <span
              class="status-badge"
              :class="performance.status"
            >
              {{ performance.status }}
            </span>
          </div>

          <div class="metrics-grid">
            <div class="metric">
              <div class="metric-label">Accuracy</div>
              <div class="metric-value">{{ (performance.accuracy * 100).toFixed(1) }}%</div>
              <div class="progress-bar">
                <div
                  class="progress-fill"
                  :style="{ width: (performance.accuracy * 100) + '%' }"
                  :class="getProgressClass(performance.accuracy)"
                ></div>
              </div>
            </div>

            <div class="metric">
              <div class="metric-label">Precision</div>
              <div class="metric-value">{{ (performance.precision * 100).toFixed(1) }}%</div>
              <div class="progress-bar">
                <div
                  class="progress-fill"
                  :style="{ width: (performance.precision * 100) + '%' }"
                  :class="getProgressClass(performance.precision)"
                ></div>
              </div>
            </div>

            <div class="metric">
              <div class="metric-label">Recall</div>
              <div class="metric-value">{{ (performance.recall * 100).toFixed(1) }}%</div>
              <div class="progress-bar">
                <div
                  class="progress-fill"
                  :style="{ width: (performance.recall * 100) + '%' }"
                  :class="getProgressClass(performance.recall)"
                ></div>
              </div>
            </div>

            <div class="metric">
              <div class="metric-label">F1 Score</div>
              <div class="metric-value">{{ (performance.f1_score * 100).toFixed(1) }}%</div>
              <div class="progress-bar">
                <div
                  class="progress-fill"
                  :style="{ width: (performance.f1_score * 100) + '%' }"
                  :class="getProgressClass(performance.f1_score)"
                ></div>
              </div>
            </div>
          </div>

          <div class="samples-info">
            {{ performance.total_samples }} samples analyzed
          </div>
        </div>
      </div>

      <!-- Current Thresholds -->
      <div class="thresholds-section">
        <h3>Current Quality Thresholds</h3>
        <p class="note">These thresholds are automatically adjusted based on feedback</p>
        
        <div class="thresholds-grid">
          <div
            v-for="(value, key) in dashboard.current_thresholds"
            :key="key"
            class="threshold-item"
          >
            <div class="threshold-label">{{ formatThresholdName(key) }}</div>
            <div class="threshold-value">{{ value }}</div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="actions-section">
        <button
          class="btn btn-primary"
          @click="triggerLearning"
          :disabled="training"
        >
          <span v-if="training">Training...</span>
          <span v-else>🧠 Re-train Algorithm</span>
        </button>
        
        <button class="btn btn-secondary" @click="refreshDashboard">
          🔄 Refresh Data
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PerformanceDashboard',

  data() {
    return {
      loading: true,
      training: false,
      dashboard: {
        overall_improvement: 0,
        component_performance: {},
        total_feedback_items: 0,
        feedback_last_30_days: 0,
        current_thresholds: {}
      }
    };
  },

  mounted() {
    this.loadDashboard();
  },

  methods: {
    async loadDashboard() {
      this.loading = true;
      
      try {
        const response = await axios.get('/api/feedback/performance');
        this.dashboard = response.data.dashboard;
      } catch (error) {
        console.error('Failed to load dashboard:', error);
      } finally {
        this.loading = false;
      }
    },

    async triggerLearning() {
      this.training = true;

      try {
        const response = await axios.post('/api/feedback/train');
        
        alert('Algorithm re-trained successfully!\n\n' + 
              'New thresholds and patterns have been learned from user feedback.');
        
        await this.loadDashboard();
      } catch (error) {
        console.error('Training failed:', error);
        alert('Failed to train algorithm. Please try again.');
      } finally {
        this.training = false;
      }
    },

    refreshDashboard() {
      this.loadDashboard();
    },

    formatComponentName(component) {
      return component
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    },

    formatThresholdName(key) {
      return key
        .replace(/_/g, ' ')
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    },

    getProgressClass(value) {
      if (value >= 0.9) return 'excellent';
      if (value >= 0.8) return 'good';
      if (value >= 0.7) return 'fair';
      return 'poor';
    }
  }
};
</script>

<style scoped>
.performance-dashboard {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.performance-dashboard h2 {
  font-size: 32px;
  font-weight: 600;
  margin-bottom: 5px;
}

.subtitle {
  color: #666;
  margin-bottom: 30px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.stat-card.improvement {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.stat-value {
  font-size: 48px;
  font-weight: 700;
  margin-bottom: 10px;
}

.stat-label {
  font-size: 16px;
  font-weight: 500;
  opacity: 0.9;
}

.stat-sublabel {
  font-size: 12px;
  opacity: 0.7;
  margin-top: 5px;
}

.component-performance {
  margin-bottom: 40px;
}

.component-performance h3 {
  font-size: 24px;
  margin-bottom: 20px;
}

.component-card {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
}

.component-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f0f0f0;
}

.component-header h4 {
  margin: 0;
  font-size: 20px;
}

.status-badge {
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge.excellent {
  background: #d4edda;
  color: #155724;
}

.status-badge.good {
  background: #d1ecf1;
  color: #0c5460;
}

.status-badge.fair {
  background: #fff3cd;
  color: #856404;
}

.status-badge.needs_improvement {
  background: #f8d7da;
  color: #721c24;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 15px;
}

.metric {
  text-align: center;
}

.metric-label {
  font-size: 14px;
  color: #666;
  margin-bottom: 8px;
}

.metric-value {
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 10px;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: #f0f0f0;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  transition: width 0.3s ease;
}

.progress-fill.excellent {
  background: #28a745;
}

.progress-fill.good {
  background: #17a2b8;
}

.progress-fill.fair {
  background: #ffc107;
}

.progress-fill.poor {
  background: #dc3545;
}

.samples-info {
  text-align: center;
  color: #666;
  font-size: 13px;
  margin-top: 15px;
}

.thresholds-section {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.thresholds-section h3 {
  font-size: 24px;
  margin-bottom: 10px;
}

.note {
  color: #666;
  font-size: 14px;
  margin-bottom: 20px;
}

.thresholds-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 15px;
}

.threshold-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  background: #f9f9f9;
  border-radius: 6px;
}

.threshold-label {
  font-size: 14px;
  font-weight: 500;
}

.threshold-value {
  font-size: 20px;
  font-weight: 700;
  color: #667eea;
}

.actions-section {
  display: flex;
  gap: 15px;
  justify-content: center;
}

.btn {
  padding: 12px 30px;
  border-radius: 6px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.3s ease;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #5568d3;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
}

.loading-spinner {
  text-align: center;
  padding: 60px;
}
</style>
