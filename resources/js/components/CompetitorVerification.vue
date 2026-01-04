<template>
  <div class="competitor-verification">
    <div class="verification-header">
      <h2>Verify Competitors</h2>
      <p class="text-muted">
        Help us improve by verifying the accuracy of these results
      </p>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
      </div>
      <p>Loading competitors...</p>
    </div>

    <div v-else-if="competitors.length === 0" class="empty-state">
      <p>No competitors to verify</p>
    </div>

    <div v-else class="competitors-list">
      <div
        v-for="(competitor, index) in competitors"
        :key="competitor.id"
        class="competitor-card"
        :class="{ 'verified': competitor.verified }"
      >
        <div class="competitor-header">
          <h3>{{ competitor.business_name }}</h3>
          <span
            v-if="competitor.verified"
            class="badge badge-success"
          >
            ✓ Verified
          </span>
        </div>

        <div class="competitor-details">
          <div class="detail-row">
            <strong>Website:</strong>
            <a :href="competitor.website" target="_blank">
              {{ competitor.website }}
            </a>
          </div>

          <div v-if="competitor.phone" class="detail-row">
            <strong>Phone:</strong> {{ competitor.phone }}
          </div>

          <div v-if="competitor.address" class="detail-row">
            <strong>Address:</strong> {{ competitor.address }}
          </div>

          <div v-if="hasSocialProfiles(competitor)" class="detail-row">
            <strong>Social Media:</strong>
            <div class="social-links">
              <a
                v-if="competitor.facebook_handle"
                :href="`https://facebook.com/${competitor.facebook_handle}`"
                target="_blank"
                class="social-link"
              >
                Facebook
              </a>
              <a
                v-if="competitor.instagram_handle"
                :href="`https://instagram.com/${competitor.instagram_handle}`"
                target="_blank"
                class="social-link"
              >
                Instagram
              </a>
              <a
                v-if="competitor.twitter_handle"
                :href="`https://twitter.com/${competitor.twitter_handle}`"
                target="_blank"
                class="social-link"
              >
                Twitter
              </a>
            </div>
          </div>
        </div>

        <div v-if="!competitor.verified" class="verification-section">
          <h4>Is this information correct?</h4>

          <div class="verification-questions">
            <!-- Relevance -->
            <div class="question-group">
              <label>Is this a relevant competitor?</label>
              <div class="btn-group">
                <button
                  class="btn btn-sm"
                  :class="{ 'btn-success': competitor.feedback.is_relevant === true }"
                  @click="setFeedback(competitor, 'is_relevant', true)"
                >
                  Yes
                </button>
                <button
                  class="btn btn-sm"
                  :class="{ 'btn-danger': competitor.feedback.is_relevant === false }"
                  @click="setFeedback(competitor, 'is_relevant', false)"
                >
                  No
                </button>
              </div>
            </div>

            <!-- Usefulness -->
            <div class="question-group">
              <label>Is this information useful?</label>
              <div class="btn-group">
                <button
                  class="btn btn-sm"
                  :class="{ 'btn-success': competitor.feedback.is_useful === true }"
                  @click="setFeedback(competitor, 'is_useful', true)"
                >
                  Yes
                </button>
                <button
                  class="btn btn-sm"
                  :class="{ 'btn-danger': competitor.feedback.is_useful === false }"
                  @click="setFeedback(competitor, 'is_useful', false)"
                >
                  No
                </button>
              </div>
            </div>

            <!-- Accuracy -->
            <div class="question-group">
              <label>Is the data accurate?</label>
              <div class="btn-group">
                <button
                  class="btn btn-sm"
                  :class="{ 'btn-success': competitor.feedback.is_accurate === true }"
                  @click="setFeedback(competitor, 'is_accurate', true)"
                >
                  Yes
                </button>
                <button
                  class="btn btn-sm"
                  :class="{ 'btn-danger': competitor.feedback.is_accurate === false }"
                  @click="setFeedback(competitor, 'is_accurate', false)"
                >
                  No
                </button>
              </div>
            </div>

            <!-- Spam -->
            <div class="question-group">
              <label>Is this spam?</label>
              <button
                class="btn btn-sm btn-outline-danger"
                :class="{ 'active': competitor.feedback.is_spam }"
                @click="toggleFeedback(competitor, 'is_spam')"
              >
                Report as Spam
              </button>
            </div>

            <!-- Duplicate -->
            <div class="question-group">
              <label>Is this a duplicate?</label>
              <button
                class="btn btn-sm btn-outline-warning"
                :class="{ 'active': competitor.feedback.is_duplicate }"
                @click="toggleFeedback(competitor, 'is_duplicate')"
              >
                Mark as Duplicate
              </button>
            </div>

            <!-- Rating -->
            <div class="question-group">
              <label>Overall Rating (1-5):</label>
              <div class="rating-stars">
                <button
                  v-for="star in 5"
                  :key="star"
                  class="star-btn"
                  :class="{ 'active': competitor.feedback.overall_rating >= star }"
                  @click="setRating(competitor, star)"
                >
                  ★
                </button>
              </div>
            </div>

            <!-- Comments -->
            <div class="question-group">
              <label>Additional Comments (optional):</label>
              <textarea
                v-model="competitor.feedback.comments"
                class="form-control"
                rows="2"
                placeholder="Any additional feedback or corrections..."
              ></textarea>
            </div>
          </div>

          <div class="verification-actions">
            <button
              class="btn btn-primary"
              @click="submitFeedback(competitor, index)"
              :disabled="!canSubmit(competitor)"
            >
              Submit Verification
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="competitors.length > 0 && allVerified" class="completion-section">
      <div class="alert alert-success">
        <h4>✓ All Competitors Verified!</h4>
        <p>Thank you for your feedback. This helps improve our results.</p>
        <button class="btn btn-success" @click="finishVerification">
          Continue to Report
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CompetitorVerification',
  
  props: {
    researchRequestId: {
      type: Number,
      required: true
    }
  },

  data() {
    return {
      loading: true,
      competitors: [],
      submitting: false
    };
  },

  computed: {
    allVerified() {
      return this.competitors.every(c => c.verified);
    }
  },

  mounted() {
    this.loadCompetitors();
  },

  methods: {
    async loadCompetitors() {
      try {
        const response = await axios.get(
          `/api/market-research/${this.researchRequestId}/report`
        );
        
        this.competitors = response.data.report.competitors.map(c => ({
          ...c,
          verified: false,
          feedback: {
            is_relevant: null,
            is_useful: null,
            is_accurate: null,
            is_spam: false,
            is_duplicate: false,
            overall_rating: 0,
            comments: ''
          }
        }));

        this.loading = false;
      } catch (error) {
        console.error('Failed to load competitors:', error);
        this.loading = false;
      }
    },

    setFeedback(competitor, field, value) {
      competitor.feedback[field] = value;
    },

    toggleFeedback(competitor, field) {
      competitor.feedback[field] = !competitor.feedback[field];
    },

    setRating(competitor, rating) {
      competitor.feedback.overall_rating = rating;
    },

    canSubmit(competitor) {
      return competitor.feedback.is_relevant !== null ||
             competitor.feedback.is_useful !== null ||
             competitor.feedback.is_accurate !== null;
    },

    async submitFeedback(competitor, index) {
      if (this.submitting) return;
      
      this.submitting = true;

      try {
        await axios.post('/api/feedback/competitor', {
          competitor_id: competitor.id,
          research_request_id: this.researchRequestId,
          feedback_type: 'relevance',
          ...competitor.feedback
        });

        competitor.verified = true;
        
        this.$nextTick(() => {
          // Scroll to next competitor
          if (index < this.competitors.length - 1) {
            const nextCard = document.querySelectorAll('.competitor-card')[index + 1];
            if (nextCard) {
              nextCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          }
        });
      } catch (error) {
        console.error('Failed to submit feedback:', error);
        alert('Failed to submit feedback. Please try again.');
      } finally {
        this.submitting = false;
      }
    },

    hasSocialProfiles(competitor) {
      return competitor.facebook_handle ||
             competitor.instagram_handle ||
             competitor.twitter_handle ||
             competitor.linkedin_url;
    },

    finishVerification() {
      this.$emit('verification-complete');
      // Navigate to report or next step
      window.location.href = `/market-research/${this.researchRequestId}/report`;
    }
  }
};
</script>

<style scoped>
.competitor-verification {
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
}

.verification-header {
  text-align: center;
  margin-bottom: 30px;
}

.verification-header h2 {
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 10px;
}

.competitor-card {
  background: white;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  transition: all 0.3s ease;
}

.competitor-card.verified {
  border-color: #28a745;
  opacity: 0.7;
}

.competitor-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  padding-bottom: 15px;
  border-bottom: 1px solid #e0e0e0;
}

.competitor-header h3 {
  font-size: 22px;
  margin: 0;
}

.competitor-details {
  margin-bottom: 20px;
}

.detail-row {
  margin-bottom: 10px;
  padding: 8px 0;
}

.detail-row strong {
  display: inline-block;
  min-width: 120px;
  color: #666;
}

.social-links {
  display: inline-flex;
  gap: 10px;
}

.social-link {
  padding: 4px 12px;
  background: #f0f0f0;
  border-radius: 4px;
  text-decoration: none;
  color: #333;
  font-size: 14px;
}

.social-link:hover {
  background: #e0e0e0;
}

.verification-section {
  background: #f9f9f9;
  padding: 20px;
  border-radius: 6px;
  margin-top: 20px;
}

.verification-section h4 {
  font-size: 18px;
  margin-bottom: 20px;
}

.verification-questions {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.question-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.question-group label {
  font-weight: 500;
  color: #333;
  margin: 0;
}

.btn-group {
  display: flex;
  gap: 10px;
}

.rating-stars {
  display: flex;
  gap: 5px;
}

.star-btn {
  background: none;
  border: none;
  font-size: 28px;
  color: #ddd;
  cursor: pointer;
  transition: color 0.2s;
}

.star-btn.active {
  color: #ffc107;
}

.star-btn:hover {
  color: #ffb300;
}

.verification-actions {
  margin-top: 20px;
  text-align: right;
}

.completion-section {
  margin-top: 30px;
}

.loading-state,
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #666;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
  margin-bottom: 15px;
}
</style>
