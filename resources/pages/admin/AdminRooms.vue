<template>
  <AdminLayout page="rooms">

    <!-- Page Header -->
    <div class="page-header rooms-header">
      <div>
        <h1 class="page-title">Room Management</h1>
        <p class="page-subtitle">Add, edit, and manage all resort rooms.</p>
      </div>
      <button class="btn btn-primary add-room-btn" @click="openModal(null, 'add')">
        + Add New Room
      </button>
    </div>

    <!-- Toolbar: Filter + View Toggle -->
    <div class="rooms-toolbar">
      <div class="filter-bar">
        <div class="filter-group">
          <label class="filter-label">Type</label>
          <select class="filter-select" v-model="filterType">
            <option value="">All Types</option>
            <option value="Standard">Standard</option>
            <option value="Deluxe">Deluxe</option>
            <option value="Suite">Suite</option>
            <option value="Family">Family</option>
          </select>
        </div>
        <div class="filter-group">
          <label class="filter-label">Availability</label>
          <select class="filter-select" v-model="filterAvailability">
            <option value="">All</option>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
          </select>
        </div>
        <button class="filter-reset" @click="resetFilters">✕ Reset</button>
      </div>

      <!-- View Toggle -->
      <div class="view-toggle">
        <button class="toggle-btn" :class="{ active: viewMode === 'grid' }" @click="viewMode = 'grid'" title="Card View">
          ⊞
        </button>
        <button class="toggle-btn" :class="{ active: viewMode === 'table' }" @click="viewMode = 'table'" title="Table View">
          ☰
        </button>
      </div>
    </div>

    <!-- ── Card Grid View ─────────────────────── -->
    <div v-if="viewMode === 'grid'" class="rooms-grid">
      <div
        v-for="room in filteredRooms"
        :key="room.id"
        class="room-card"
        :class="{ unavailable: !room.available }"
      >
        <!-- Room Photo -->
        <div class="room-photo">
          <img :src="room.photo" :alt="room.name" />
          <div class="room-photo-overlay">
            <span class="availability-pill" :class="room.available ? 'pill-available' : 'pill-unavailable'">
              {{ room.available ? 'Available' : 'Unavailable' }}
            </span>
          </div>
        </div>

        <!-- Room Info -->
        <div class="room-info">
          <div class="room-info-top">
            <div>
              <div class="room-number">Room {{ room.number }}</div>
              <div class="room-name">{{ room.name }}</div>
            </div>
            <div class="room-price">
              <span class="price-value">₱{{ parseFloat(room.price_per_night).toLocaleString() }}</span>
              <span class="price-label">/night</span>
            </div>
          </div>

          <div class="room-meta">
            <span class="room-type-badge">{{ room.type }}</span>
            <span class="room-capacity">👥 Up to {{ room.capacity }} guests</span>
          </div>

          <!-- Card Actions -->
          <div class="room-card-actions">
            <button
              class="card-action-btn toggle-btn-room"
              :class="room.available ? 'btn-toggle-off' : 'btn-toggle-on'"
              @click="toggleAvailability(room)"
              :title="room.available ? 'Mark Unavailable' : 'Mark Available'"
            >
              {{ room.available ? '🔒 Set Unavailable' : '🔓 Set Available' }}
            </button>
            <div class="card-action-icons">
              <button class="action-btn edit" title="Edit" @click="openModal(room, 'edit')">✏️</button>
              <button class="action-btn cancel" title="Delete" @click="openModal(room, 'delete')">🗑</button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredRooms.length === 0" class="empty-state">
        <div class="empty-icon">🛏️</div>
        <div class="empty-title">No rooms found</div>
        <div class="empty-text">Try adjusting your filters or add a new room.</div>
      </div>
    </div>

    <!-- ── Table View ─────────────────────────── -->
    <div v-if="viewMode === 'table'" class="admin-card">
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Room</th>
              <th>Type</th>
              <th>Capacity</th>
              <th>Price / Night</th>
              <th>Availability</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="room in filteredRooms" :key="room.id">
              <td>
                <div class="table-room-cell">
                  <img :src="room.photo" :alt="room.name" class="table-room-thumb" />
                  <div>
                    <div class="guest-name">{{ room.name }}</div>
                    <div class="guest-email">Room {{ room.number }}</div>
                  </div>
                </div>
              </td>
              <td><span class="room-type-badge">{{ room.type }}</span></td>
              <td>{{ room.capacity }} guests</td>
              <td><span class="booking-id">₱{{ parseFloat(room.price_per_night).toLocaleString() }}</span></td>
              <td>
                <span class="badge" :class="room.available ? 'badge-confirmed' : 'badge-cancelled'">
                  {{ room.available ? 'Available' : 'Unavailable' }}
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <button
                    class="action-btn"
                    :title="room.available ? 'Set Unavailable' : 'Set Available'"
                    @click="toggleAvailability(room)"
                  >
                    {{ room.available ? '🔒' : '🔓' }}
                  </button>
                  <button class="action-btn edit" title="Edit" @click="openModal(room, 'edit')">✏️</button>
                  <button class="action-btn cancel" title="Delete" @click="openModal(room, 'delete')">🗑</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="filteredRooms.length === 0" class="empty-state">
          <div class="empty-icon">🛏️</div>
          <div class="empty-title">No rooms found</div>
          <div class="empty-text">Try adjusting your filters or add a new room.</div>
        </div>
      </div>
    </div>

    <!-- ── Modal ─────────────────────────────── -->
    <Transition name="fade">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-box booking-modal">

          <!-- Add / Edit Mode -->
          <template v-if="modalMode === 'add' || modalMode === 'edit'">
            <div class="modal-header">
              <div>
                <h3 class="modal-title">{{ modalMode === 'add' ? 'Add New Room' : 'Edit Room' }}</h3>
              </div>
            </div>
            <div class="modal-form">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Room Name</label>
                  <input class="form-input" type="text" v-model="roomForm.name" placeholder="e.g. Deluxe Suite" />
                </div>
                <div class="form-group">
                  <label class="form-label">Room Number</label>
                  <input class="form-input" type="text" v-model="roomForm.number" placeholder="e.g. 301" />
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Room Type</label>
                  <select class="form-input" v-model="roomForm.type">
                    <option value="Standard">Standard</option>
                    <option value="Deluxe">Deluxe</option>
                    <option value="Suite">Suite</option>
                    <option value="Family">Family</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Capacity</label>
                  <input class="form-input" type="number" v-model.number="roomForm.capacity" min="1" placeholder="e.g. 2" />
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Price per Night (₱)</label>
                <input class="form-input" type="number" v-model.number="roomForm.price_per_night" min="0" placeholder="e.g. 3500" />
              </div>
              <div class="form-group">
                <label class="form-label">Photo URL</label>
                <input class="form-input" type="text" v-model="roomForm.photo" placeholder="https://..." />
              </div>
              <div class="form-group">
                <label class="form-label">Availability</label>
                <select class="form-input" v-model="roomForm.available">
                  <option :value="true">Available</option>
                  <option :value="false">Unavailable</option>
                </select>
              </div>
              <div class="form-group">
                <div class="discount-header">
                  <label class="form-label">Discount (%)</label>
                  <span class="discount-value">{{ roomForm.discount }}%</span>
                </div>
                <input class="form-slider" type="range" v-model.number="roomForm.discount" min="0" max="100" step="1" />
                <div class="discount-labels">
                  <span>0%</span>
                  <span>100%</span>
                </div>
              </div>
            </div>
            <div class="modal-actions">
              <button class="btn btn-secondary" @click="closeModal">Cancel</button>
              <button class="btn btn-primary" @click="saveRoom">
                {{ modalMode === 'add' ? 'Add Room' : 'Save Changes' }}
              </button>
            </div>
          </template>

          <!-- Delete Mode -->
          <template v-else-if="modalMode === 'delete'">
            <div class="modal-icon">🗑️</div>
            <h3 class="modal-title">Delete Room?</h3>
            <p class="modal-text">
              This will permanently delete <strong>{{ selectedRoom.name }}</strong> (Room {{ selectedRoom.number }}). This action cannot be undone.
            </p>
            <div class="modal-actions" style="justify-content: center;">
              <button class="btn btn-secondary" @click="closeModal">Cancel</button>
              <button class="btn btn-danger" @click="deleteRoom">Yes, Delete</button>
            </div>
          </template>

        </div>
      </div>
    </Transition>

  </AdminLayout>
</template>

<script>
import AdminLayout from '../../components/AdminLayout.vue'

export default {    
  name: 'AdminRooms',
  components: { AdminLayout },

  props: {
    rooms: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      viewMode: 'grid',         // 'grid' | 'table'
      filterType: '',
      filterAvailability: '',

      showModal: false,
      modalMode: 'add',         // 'add' | 'edit' | 'delete'
      selectedRoom: null,
      roomForm: this.emptyForm()
    }
  },

  computed: {
    filteredRooms() {
      let filtered = this.rooms

      if (this.filterType) {
        filtered = filtered.filter(r => r.type === this.filterType)
      }

      if (this.filterAvailability === 'available') {
        filtered = filtered.filter(r => r.available)
      } else if (this.filterAvailability === 'unavailable') {
        filtered = filtered.filter(r => !r.available)
      }

      return filtered
    }
  },

  methods: {
    emptyForm() {
      return {
        number: '',
        name: '',
        type: 'Standard',
        capacity: 1,
        price_per_night: 0,
        available: true,
        photo: '',
        discount: 0
      }
    },

    openModal(room, mode) {
      this.modalMode = mode
      this.selectedRoom = room

      if (mode === 'add') {
        this.roomForm = this.emptyForm()
      } else if (mode === 'edit') {
        this.roomForm = { ...room }
      }

      this.showModal = true
    },

    closeModal() {
      this.showModal = false
      this.selectedRoom = null
      this.roomForm = this.emptyForm()
    },

    saveRoom() {
      if (this.modalMode === 'add') {
        this.$inertia.post('/rooms', this.roomForm, {
          onSuccess: () => this.closeModal()
        })
      } else {
        this.$inertia.put(`/rooms/${this.selectedRoom.id}`, this.roomForm, {
          onSuccess: () => this.closeModal()
        })
      }
    },

    deleteRoom() {
      this.$inertia.delete(`/rooms/${this.selectedRoom.id}`, {
        onSuccess: () => this.closeModal()
      })
    },

    toggleAvailability(room) {
      this.$inertia.post(`/rooms/${room.id}/toggle-availability`, {}, {
        onSuccess: () => {
          room.available = !room.available
        }
      })
    },

    resetFilters() {
      this.filterType = ''
      this.filterAvailability = ''
    }
  }
}
</script>

<style scoped>
/* ─── Page Header ────────────────────── */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
}

.rooms-header {
  flex-wrap: wrap;
  gap: 16px;
}

.page-title {
  font-size: 24px;
  font-weight: 800;
  color: #111827;
  margin: 0 0 4px 0;
}

.page-subtitle {
  font-size: 13.5px;
  color: #6B7280;
  margin: 0;
}

.add-room-btn {
  height: 38px;
  padding: 0 18px;
  border-radius: 8px;
  background: var(--admin-blue, #00B4FF);
  color: #fff;
  border: none;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}

.add-room-btn:hover {
  background: #0099CC;
}

/* ─── Toolbar ────────────────────────── */
.rooms-toolbar {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.filter-bar {
  display: flex;
  align-items: flex-end;
  gap: 12px;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.filter-label {
  font-size: 11px;
  font-weight: 600;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.filter-select {
  height: 36px;
  padding: 0 12px;
  border-radius: 8px;
  border: 1px solid var(--admin-border, #E5E7EB);
  background: #fff;
  font-size: 13.5px;
  color: #111827;
  cursor: pointer;
  outline: none;
  transition: border-color 0.15s;
}

.filter-select:focus {
  border-color: var(--admin-blue, #00B4FF);
}

.filter-reset {
  height: 36px;
  padding: 0 14px;
  border-radius: 8px;
  border: 1px solid var(--admin-border, #E5E7EB);
  background: #fff;
  font-size: 12.5px;
  color: var(--admin-text-muted, #6B7280);
  cursor: pointer;
  transition: all 0.15s;
}

.filter-reset:hover {
  background: #FEE2E2;
  border-color: #FECACA;
  color: #DC2626;
}

/* ─── View Toggle ────────────────────── */
.view-toggle {
  display: flex;
  gap: 6px;
  padding: 4px;
  background: #F3F4F6;
  border-radius: 8px;
}

.toggle-btn {
  width: 34px;
  height: 34px;
  border-radius: 6px;
  border: 1px solid transparent;
  background: transparent;
  font-size: 16px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}

.toggle-btn.active {
  background: #fff;
  border-color: var(--admin-border, #E5E7EB);
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.toggle-btn:hover {
  background: rgba(0,180,255,0.08);
}

/* ─── Grid View ──────────────────────── */
.rooms-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 32px;
}

.room-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  transition: all 0.2s;
}

.room-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.room-card.unavailable {
  opacity: 0.7;
}

.room-photo {
  position: relative;
  width: 100%;
  height: 160px;
  overflow: hidden;
  background: #F3F4F6;
}

.room-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.room-photo-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.3), transparent);
  display: flex;
  align-items: flex-start;
  padding: 10px;
}

.availability-pill {
  background: #10B981;
  color: #fff;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}

.pill-unavailable {
  background: #EF4444 !important;
}

/* ─── Room Info ──────────────────────── */
.room-info {
  padding: 16px;
}

.room-info-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 12px;
}

.room-number {
  font-size: 11px;
  font-weight: 600;
  color: var(--admin-text-muted, #6B7280);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 2px;
}

.room-name {
  font-size: 14.5px;
  font-weight: 700;
  color: #111827;
}

.room-price {
  text-align: right;
}

.price-value {
  font-size: 15px;
  font-weight: 800;
  color: #111827;
}

.price-label {
  font-size: 11px;
  color: var(--admin-text-muted, #6B7280);
  display: block;
}

.room-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 14px;
}

.room-type-badge {
  background: #EFF6FF;
  color: #2563EB;
  border: 1px solid #BFDBFE;
  padding: 2px 8px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
}

.room-capacity {
  font-size: 12px;
  color: var(--admin-text-muted, #6B7280);
}

/* ─── Card Actions ───────────────────── */
.room-card-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.card-action-btn {
  flex: 1;
  height: 32px;
  border-radius: 8px;
  border: 1px solid var(--admin-border, #E5E7EB);
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-toggle-off {
  background: #FEF2F2;
  border-color: #FECACA;
  color: #DC2626;
}

.btn-toggle-off:hover {
  background: #FEE2E2;
}

.btn-toggle-on {
  background: #ECFDF5;
  border-color: #A7F3D0;
  color: #059669;
}

.btn-toggle-on:hover {
  background: #D1FAE5;
}

.card-action-icons {
  display: flex;
  gap: 5px;
}

/* ─── Table Room Cell ────────────────── */
.table-room-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}

.table-room-thumb {
  width: 44px;
  height: 36px;
  border-radius: 6px;
  object-fit: cover;
  flex-shrink: 0;
}

/* ─── Action Buttons ─────────────────── */
.action-buttons {
  display: flex;
  gap: 5px;
}

.action-btn {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  border: 1px solid var(--admin-border, #E5E7EB);
  background: #fff;
  font-size: 13px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}

.action-btn:hover       { transform: translateY(-1px); }
.action-btn.edit:hover  { background: #FFFBEB; border-color: #FDE68A; }
.action-btn.cancel:hover { background: #FEF2F2; border-color: #FECACA; }

.booking-id {
  font-family: var(--font-mono, monospace);
  font-size: 12px;
  color: var(--admin-blue, #00B4FF);
  font-weight: 600;
}

.guest-name {
  font-size: 13.5px;
  font-weight: 500;
  color: var(--admin-text, #111827);
}

.guest-email {
  font-size: 11.5px;
  color: var(--admin-text-muted, #6B7280);
}

/* ─── Modal ──────────────────────────── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

.modal-box {
  background: #fff;
  border-radius: 16px;
  padding: 32px;
  width: 460px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  text-align: center;
  max-height: 90vh;
  overflow-y: auto;
}

.booking-modal {
  text-align: left;
}

.modal-header {
  margin-bottom: 20px;
}

.modal-title {
  font-size: 17px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 8px;
}

.modal-text {
  font-size: 13.5px;
  color: #6B7280;
  margin-bottom: 24px;
  line-height: 1.6;
  text-align: center;
}

.modal-icon {
  font-size: 36px;
  margin-bottom: 12px;
  text-align: center;
}

.modal-form {
  margin-bottom: 24px;
}

.form-group {
  margin-bottom: 14px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.form-label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #6B7280;
  margin-bottom: 5px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.form-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid var(--admin-border, #E5E7EB);
  border-radius: 8px;
  font-size: 13.5px;
  color: #111827;
  outline: none;
  transition: border-color 0.15s;
  box-sizing: border-box;
}

.form-input:focus {
  border-color: var(--admin-blue, #00B4FF);
}

/* ─── Discount Slider ────────────────── */
.discount-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 8px;
}

.discount-value {
  font-size: 14px;
  font-weight: 700;
  color: var(--admin-blue, #00B4FF);
}

.form-slider {
  width: 100%;
  height: 6px;
  border-radius: 3px;
  background: #E5E7EB;
  outline: none;
  -webkit-appearance: none;
  appearance: none;
  margin: 10px 0;
}

.form-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: var(--admin-blue, #00B4FF);
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  transition: all 0.2s;
}

.form-slider::-webkit-slider-thumb:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 8px rgba(0, 180, 255, 0.4);
}

.form-slider::-moz-range-thumb {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: var(--admin-blue, #00B4FF);
  cursor: pointer;
  border: none;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  transition: all 0.2s;
}

.form-slider::-moz-range-thumb:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 8px rgba(0, 180, 255, 0.4);
}

.discount-labels {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  color: #9CA3AF;
  margin-top: 4px;
}

.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.btn {
  padding: 8px 18px;
  border-radius: 8px;
  border: none;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-primary {
  background: var(--admin-blue, #00B4FF);
  color: #fff;
}

.btn-primary:hover:not(:disabled) {
  background: #0099CC;
}

.btn-secondary {
  background: #F3F4F6;
  color: #6B7280;
  border: 1px solid var(--admin-border, #E5E7EB);
}

.btn-secondary:hover:not(:disabled) {
  background: #E5E7EB;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-danger {
  background: #DC2626;
  color: #fff;
}

.btn-danger:hover:not(:disabled) {
  background: #B91C1C;
}

/* ─── Empty State ────────────────────── */
.empty-state {
  text-align: center;
  padding: 48px 24px;
  color: #6B7280;
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 12px;
}

.empty-title {
  font-size: 15px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 4px;
}

.empty-text {
  font-size: 13px;
  color: #9CA3AF;
}

/* ─── Table Styles ──────────────────── */
.admin-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.admin-table-wrap {
  overflow-x: auto;
}

.admin-table {
  width: 100%;
  border-collapse: collapse;
}

.admin-table thead tr {
  border-bottom: 1px solid var(--admin-border, #E5E7EB);
  background: #F9FAFB;
}

.admin-table th {
  padding: 12px 16px;
  text-align: left;
  font-size: 12px;
  font-weight: 600;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.admin-table tbody tr {
  border-bottom: 1px solid var(--admin-border, #E5E7EB);
  transition: background 0.15s;
}

.admin-table tbody tr:hover {
  background: #F9FAFB;
}

.admin-table td {
  padding: 12px 16px;
  font-size: 13.5px;
  color: #111827;
}

.badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 600;
}

.badge-confirmed {
  background: #ECFDF5;
  color: #059669;
}

.badge-cancelled {
  background: #FEE2E2;
  color: #DC2626;
}

/* ─── Fade Transition ────────────────── */
.fade-enter-active,
.fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from,
.fade-leave-to { opacity: 0; }
</style>