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
              <span class="price-value">₱{{ room.price.toLocaleString() }}</span>
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
              <td><span class="booking-id">₱{{ room.price.toLocaleString() }}</span></td>
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
                  <input class="form-input" type="number" v-model="roomForm.capacity" min="1" placeholder="e.g. 2" />
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Price per Night (₱)</label>
                <input class="form-input" type="number" v-model="roomForm.price" min="0" placeholder="e.g. 3500" />
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

  data() {
    return {
      viewMode: 'grid',         // 'grid' | 'table'
      filterType: '',
      filterAvailability: '',

      showModal: false,
      modalMode: 'add',         // 'add' | 'edit' | 'delete'
      selectedRoom: null,
      roomForm: this.emptyForm(),

      // Placeholder data — replace with API calls later
      rooms: [
        { id: 1, number: '101', name: 'Standard Room',   type: 'Standard', capacity: 2, price: 2500,  available: true,  photo: 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&q=80' },
        { id: 2, number: '102', name: 'Standard Room',   type: 'Standard', capacity: 2, price: 2500,  available: false, photo: 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=400&q=80' },
        { id: 3, number: '201', name: 'Deluxe Room',     type: 'Deluxe',   capacity: 2, price: 4200,  available: true,  photo: 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=400&q=80' },
        { id: 4, number: '205', name: 'Deluxe Room',     type: 'Deluxe',   capacity: 3, price: 4800,  available: true,  photo: 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=400&q=80' },
        { id: 5, number: '301', name: 'Deluxe Suite',    type: 'Suite',    capacity: 2, price: 7500,  available: true,  photo: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&q=80' },
        { id: 6, number: '305', name: 'Deluxe Suite',    type: 'Suite',    capacity: 4, price: 9000,  available: false, photo: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400&q=80' },
        { id: 7, number: '401', name: 'Family Suite',    type: 'Family',   capacity: 6, price: 11000, available: true,  photo: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400&q=80' },
        { id: 8, number: '402', name: 'Family Suite',    type: 'Family',   capacity: 6, price: 11000, available: true,  photo: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400&q=80' },
      ]
    }
  },

  computed: {
    filteredRooms() {
      let list = this.rooms

      if (this.filterType) {
        list = list.filter(r => r.type === this.filterType)
      }
      if (this.filterAvailability === 'available') {
        list = list.filter(r => r.available)
      } else if (this.filterAvailability === 'unavailable') {
        list = list.filter(r => !r.available)
      }

      return list
    }
  },

  methods: {
    emptyForm() {
      return { name: '', number: '', type: 'Standard', capacity: 2, price: '', photo: '', available: true }
    },

    resetFilters() {
      this.filterType = ''
      this.filterAvailability = ''
    },

    openModal(room, mode) {
      this.modalMode = mode
      this.selectedRoom = room ? { ...room } : null
      this.roomForm = mode === 'edit' ? { ...room } : this.emptyForm()
      this.showModal = true
    },

    closeModal() {
      this.showModal = false
      this.selectedRoom = null
      this.roomForm = this.emptyForm()
    },

    saveRoom() {
      if (this.modalMode === 'add') {
        const newRoom = {
          ...this.roomForm,
          id: Date.now(),
          capacity: Number(this.roomForm.capacity),
          price: Number(this.roomForm.price),
        }
        this.rooms.push(newRoom)
      } else {
        const index = this.rooms.findIndex(r => r.id === this.roomForm.id)
        if (index !== -1) {
          this.rooms.splice(index, 1, {
            ...this.roomForm,
            capacity: Number(this.roomForm.capacity),
            price: Number(this.roomForm.price),
          })
        }
      }
      this.closeModal()
    },

    deleteRoom() {
      this.rooms = this.rooms.filter(r => r.id !== this.selectedRoom.id)
      this.closeModal()
    },

    toggleAvailability(room) {
      const target = this.rooms.find(r => r.id === room.id)
      if (target) target.available = !target.available
    }
  }
}
</script>

<style scoped>
/* ─── Header ─────────────────────────── */
.rooms-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.add-room-btn {
  flex-shrink: 0;
}

/* ─── Toolbar ────────────────────────── */
.rooms-toolbar {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 14px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.filter-bar {
  display: flex;
  align-items: flex-end;
  gap: 14px;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.filter-label {
  font-size: 11.5px;
  font-weight: 600;
  color: var(--admin-text-muted, #6B7280);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.filter-select {
  height: 36px;
  padding: 0 10px;
  border: 1px solid var(--admin-border, #E5E7EB);
  border-radius: 8px;
  font-size: 13px;
  color: var(--admin-text, #111827);
  background: #fff;
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
  gap: 4px;
  border: 1px solid var(--admin-border, #E5E7EB);
  border-radius: 8px;
  padding: 3px;
  background: #fff;
}

.toggle-btn {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  border: none;
  background: transparent;
  font-size: 16px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
  color: var(--admin-text-muted, #6B7280);
}

.toggle-btn.active {
  background: var(--admin-blue, #00B4FF);
  color: #fff;
}

/* ─── Card Grid ──────────────────────── */
.rooms-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.room-card {
  background: #fff;
  border-radius: 14px;
  border: 1px solid var(--admin-border, #E5E7EB);
  overflow: hidden;
  transition: all 0.2s ease;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}

.room-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.room-card.unavailable {
  opacity: 0.72;
}

/* ─── Room Photo ─────────────────────── */
.room-photo {
  position: relative;
  height: 160px;
  overflow: hidden;
}

.room-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.room-card:hover .room-photo img {
  transform: scale(1.04);
}

.room-photo-overlay {
  position: absolute;
  top: 10px;
  right: 10px;
}

.availability-pill {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.03em;
}

.pill-available {
  background: #ECFDF5;
  color: #059669;
  border: 1px solid #A7F3D0;
}

.pill-unavailable {
  background: #FEF2F2;
  color: #DC2626;
  border: 1px solid #FECACA;
}

/* ─── Room Info ──────────────────────── */
.room-info {
  padding: 14px 16px 16px;
}

.room-info-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 10px;
}

.room-number {
  font-size: 11px;
  font-weight: 600;
  color: var(--admin-blue, #00B4FF);
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

.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.btn-danger {
  background: #DC2626;
  color: #fff;
  border: none;
  padding: 8px 18px;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}

.btn-danger:hover {
  background: #B91C1C;
}

/* ─── Fade Transition ────────────────── */
.fade-enter-active,
.fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from,
.fade-leave-to { opacity: 0; }
</style>