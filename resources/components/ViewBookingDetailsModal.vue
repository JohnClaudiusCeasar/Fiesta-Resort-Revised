<template>
  <Transition name="fade">
    <div v-if="show" class="modal-overlay" @click.self="$emit('close')">
      <div class="modal-box">
        
        <!-- VIEW MODE -->
        <template v-if="mode === 'view'">
          <div class="modal-header">
            <div>
              <div class="booking-ref">#{{ booking.display_id }}</div>
              <h3 class="modal-title">Booking Details</h3>
            </div>
            <span class="status-badge" :class="badgeClass(booking.status)">
              {{ booking.status }}
            </span>
          </div>
          
          <div class="detail-section">
            <div class="detail-row">
              <div class="detail-item">
                <span class="detail-label">Guest</span>
                <span class="detail-value">{{ booking.guest }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Room</span>
                <span class="detail-value">{{ booking.room }}</span>
              </div>
            </div>
            
            <div class="detail-row">
              <div class="detail-item">
                <span class="detail-label">Check-in</span>
                <span class="detail-value">{{ booking.checkIn }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Check-out</span>
                <span class="detail-value">{{ booking.checkOut }}</span>
              </div>
            </div>
            
            <div class="detail-row">
              <div class="detail-item full-width">
                <span class="detail-label">Special Requests</span>
                <span class="detail-value notes-value">{{ booking.notes || 'None' }}</span>
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button class="btn-secondary" @click="$emit('close')">Close</button>
          </div>
        </template>

        <!-- CONFIRM MODE -->
        <template v-else-if="mode === 'confirm'">
          <div class="modal-icon">✓</div>
          <h3 class="modal-title centered">Confirm Booking?</h3>
          <p class="modal-text">Confirm booking #{{ booking.display_id }} for {{ booking.guest }}?</p>
          <div class="modal-footer centered">
            <button class="btn-secondary" @click="$emit('close')">Cancel</button>
            <button class="btn-primary" @click="$emit('confirm')">Yes, Confirm</button>
          </div>
        </template>

        <!-- EDIT MODE -->
        <template v-else-if="mode === 'edit'">
          <div class="modal-header">
            <h3 class="modal-title">Edit Booking #{{ booking.display_id }}</h3>
          </div>
          
          <div class="modal-form">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Check-in</label>
                <input class="form-input" type="date" v-model="localEditForm.check_in" />
              </div>
              <div class="form-group">
                <label class="form-label">Check-out</label>
                <input class="form-input" type="date" v-model="localEditForm.check_out" />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Special Requests</label>
              <textarea class="form-input form-textarea" v-model="localEditForm.notes"></textarea>
            </div>
          </div>
          
          <div class="modal-footer">
            <button class="btn-secondary" @click="$emit('close')">Cancel</button>
            <button class="btn-primary" @click="handleSave">Save Changes</button>
          </div>
        </template>

        <!-- CANCEL MODE -->
        <template v-else-if="mode === 'cancel'">
          <div class="modal-icon cancel-icon">✕</div>
          <h3 class="modal-title centered">Cancel Booking?</h3>
          <p class="modal-text">This will cancel booking #{{ booking.display_id }}.</p>
          <div class="modal-footer centered">
            <button class="btn-secondary" @click="$emit('close')">Go Back</button>
            <button class="btn-danger" @click="$emit('cancel')">Yes, Cancel It</button>
          </div>
        </template>

      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  booking: { type: Object, default: () => ({}) },
  mode: { type: String, default: 'view' },
  editForm: { type: Object, default: () => ({}) }
})

const emit = defineEmits(['close', 'confirm', 'cancel', 'save'])

const localEditForm = ref({
  id: null,
  check_in: '',
  check_out: '',
  notes: ''
})

watch(() => props.editForm, (newForm) => {
  if (newForm) {
    localEditForm.value = { ...newForm }
  }
}, { immediate: true })

const badgeClass = (status) => {
  const map = {
    'Confirmed': 'badge-confirmed',
    'Pending': 'badge-pending',
    'Cancelled': 'badge-cancelled',
    'Checked In': 'badge-checked-in',
    'Checked Out': 'badge-checked-out',
  }
  return map[status] || ''
}

const handleSave = () => {
  emit('save', { ...localEditForm.value })
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
  padding: 20px;
}

.modal-box {
  background: #fff;
  border-radius: 16px;
  padding: 28px;
  width: 100%;
  max-width: 440px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
}

.modal-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 24px;
}

.booking-ref {
  font-family: 'Courier New', monospace;
  font-size: 12px;
  color: #00B4FF;
  font-weight: 600;
  margin-bottom: 4px;
}

.modal-title {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.modal-title.centered {
  text-align: center;
}

.modal-icon {
  font-size: 48px;
  text-align: center;
  margin-bottom: 12px;
  color: #10B981;
}

.cancel-icon {
  color: #EF4444;
}

.modal-text {
  font-size: 14px;
  color: #6B7280;
  margin-bottom: 24px;
  line-height: 1.6;
  text-align: center;
}

.detail-section {
  background: #F9FAFB;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 24px;
}

.detail-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  padding: 12px 0;
  border-bottom: 1px solid #E5E7EB;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.detail-item.full-width {
  grid-column: 1 / -1;
}

.detail-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #9CA3AF;
}

.detail-value {
  font-size: 14px;
  font-weight: 500;
  color: #111827;
}

.notes-value {
  font-style: italic;
  color: #6B7280;
}

.modal-footer {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

.modal-footer.centered {
  justify-content: center;
}

.modal-form {
  margin-bottom: 24px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 12px;
  font-weight: 600;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.form-input {
  padding: 10px 14px;
  border: 1px solid #E5E7EB;
  border-radius: 8px;
  font-size: 14px;
  color: #111827;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
}

.form-input:focus {
  border-color: #00B4FF;
  box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.1);
}

.form-textarea {
  min-height: 80px;
  resize: vertical;
}

.btn-secondary {
  padding: 10px 20px;
  border-radius: 8px;
  border: 1px solid #E5E7EB;
  background: #fff;
  font-size: 14px;
  font-weight: 500;
  color: #6B7280;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-secondary:hover {
  background: #F3F4F6;
  border-color: #D1D5DB;
}

.btn-primary {
  padding: 10px 20px;
  border-radius: 8px;
  border: none;
  background: linear-gradient(135deg, #00B4FF, #009CE0);
  font-size: 14px;
  font-weight: 600;
  color: #fff;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #009CE0, #0088C4);
  transform: translateY(-1px);
}

.btn-danger {
  padding: 10px 20px;
  border-radius: 8px;
  border: none;
  background: #EF4444;
  font-size: 14px;
  font-weight: 600;
  color: #fff;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-danger:hover {
  background: #DC2626;
  transform: translateY(-1px);
}

.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.badge-confirmed {
  background: #D1FAE5;
  color: #059669;
}

.badge-pending {
  background: #FEF3C7;
  color: #D97706;
}

.badge-cancelled {
  background: #FEE2E2;
  color: #DC2626;
}

.badge-checked-in {
  background: #DBEAFE;
  color: #2563EB;
}

.badge-checked-out {
  background: #F3F4F6;
  color: #6B7280;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>