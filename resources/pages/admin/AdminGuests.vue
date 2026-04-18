<template>
  <AdminLayout page="guests">

    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Guest Management</h1>
        <p class="page-subtitle">View and manage all resort guests — online and walk-in.</p>
      </div>
      <button class="btn-add-guest" @click="openModal(null, 'add')">
        + Add Walk-in Guest
      </button>
    </div>

     <!-- Stats Row -->
     <div class="stats-row">
       <div class="stat-card">
         <div class="stat-icon">
           <img src="/resources/assets/total_guests_logo.svg" alt="Total Guests" class="stat-logo">
         </div>
         <div class="stat-info">
           <div class="stat-value">{{ guests.length }}</div>
           <div class="stat-label">Total Guests</div>
         </div>
       </div>
       <div class="stat-card">
         <div class="stat-icon">
           <img src="/resources/assets/online_guests_logo.svg" alt="Online Guests" class="stat-logo">
         </div>
         <div class="stat-info">
           <div class="stat-value">{{ onlineCount }}</div>
           <div class="stat-label">Online Guests</div>
         </div>
       </div>
       <div class="stat-card">
         <div class="stat-icon">
           <img src="/resources/assets/walkin_guests_logo.svg" alt="Walk-in Guests" class="stat-logo">
         </div>
         <div class="stat-info">
           <div class="stat-value">{{ walkinCount }}</div>
           <div class="stat-label">Walk-in Guests</div>
         </div>
       </div>
       <div class="stat-card">
         <div class="stat-icon">
           <img src="/resources/assets/currently_staying_logo.svg" alt="Currently Staying" class="stat-logo">
         </div>
         <div class="stat-info">
           <div class="stat-value">{{ stayingCount }}</div>
           <div class="stat-label">Currently Staying</div>
         </div>
       </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
      <div class="filter-group">
        <label class="filter-label">Search</label>
        <input
          class="filter-input search-input"
          type="text"
          placeholder="Name or email..."
          v-model="filterSearch"
        />
      </div>

      <div class="filter-group">
        <label class="filter-label">Type</label>
        <select class="filter-select" v-model="filterType">
          <option value="">All Types</option>
          <option value="online">Online</option>
          <option value="walkin">Walk-in</option>
        </select>
      </div>

      <div class="filter-group">
        <label class="filter-label">Status</label>
        <select class="filter-select" v-model="filterStatus">
          <option value="">All Statuses</option>
          <option value="Active">Active</option>
          <option value="Checked In">Checked In</option>
          <option value="Checked Out">Checked Out</option>
          <option value="Blacklisted">Blacklisted</option>
        </select>
      </div>

      <button class="filter-reset" @click="resetFilters">✕ Reset</button>
    </div>

    <!-- Guest Table -->
    <div class="admin-card">
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Guest</th>
              <th>Type</th>
              <th>Phone</th>
              <th>Nationality</th>
              <th>Total Bookings</th>
              <th>Last Stay</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="guest in filteredGuests" :key="guest.id">
              <td>
                <div class="guest-cell">
                  <div class="guest-avatar" :class="guest.type === 'walkin' ? 'avatar-walkin' : ''">
                    {{ guest.name.charAt(0) }}
                  </div>
                  <div>
                    <div class="guest-name flex items-center gap-2">
                      <span>{{ guest.name }}</span>
                      
                      <template v-if="guest.is_active">
                        <span class="relative flex h-3 w-3" title="Active Now">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                      </template>
                    </div>
                    <div class="guest-email">{{ guest.email || '—' }}</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="type-badge" :class="guest.type === 'online' ? 'type-online' : 'type-walkin'">
                  {{ guest.type === 'online' ? '🖥️ Online' : '🚶 Walk-in' }}
                </span>
              </td>
              <td class="td-muted">{{ guest.phone || '—' }}</td>
              <td class="td-muted">{{ guest.nationality || '—' }}</td>
              <td class="td-center">{{ guest.totalBookings }}</td>
              <td class="td-muted">{{ guest.lastStay || '—' }}</td>
              <td>
                <span class="badge" :class="guestBadgeClass(guest.status)">
                  {{ guest.status }}
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <button class="action-btn view"      title="View Details"  @click="openModal(guest, 'view')">
                    <img :src="viewIcon" alt="View" />
                  </button>
                  <button class="action-btn edit"      title="Edit Guest"    @click="openModal(guest, 'edit')">
                    <img :src="editIcon" alt="Edit" />
                  </button>
                  <button class="action-btn delete"    title="Delete Guest"  @click="openModal(guest, 'delete')">
                    <img :src="trashIcon" alt="Delete" />
                  </button>
                  <button
                    class="action-btn blacklist"
                    title="Blacklist Guest"
                    @click="openModal(guest, 'blacklist')"
                    v-if="guest.status !== 'Blacklisted'"
                  >
                    <img :src="cancelIcon" alt="Blacklist" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="filteredGuests.length === 0" class="empty-state">
          <div class="empty-icon">👥</div>
          <div class="empty-title">No guests found</div>
          <div class="empty-text">Try adjusting your filters.</div>
        </div>
      </div>
    </div>

    <!-- ── Modals ──────────────────────────────────── -->
    <Transition name="fade">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-box guest-modal" :class="{ 'modal-wide': modalMode === 'view' }">

          <!-- VIEW MODE -->
          <template v-if="modalMode === 'view'">
            <div class="modal-header">
              <div class="modal-guest-profile">
                <div class="modal-avatar" :class="selectedGuest.type === 'walkin' ? 'avatar-walkin' : ''">
                  {{ selectedGuest.name.charAt(0) }}
                </div>
                <div>
                  <h3 class="modal-title">{{ selectedGuest.name }}</h3>
                  <span class="type-badge" :class="selectedGuest.type === 'online' ? 'type-online' : 'type-walkin'">
                    {{ selectedGuest.type === 'online' ? '🖥️ Online' : '🚶 Walk-in' }}
                  </span>
                </div>
              </div>
              <span class="badge" :class="guestBadgeClass(selectedGuest.status)">{{ selectedGuest.status }}</span>
            </div>

            <div class="modal-detail-grid">
              <div class="detail-item">
                <span class="detail-label">Email</span>
                <span class="detail-value">{{ selectedGuest.email || '—' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Phone</span>
                <span class="detail-value">{{ selectedGuest.phone || '—' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Nationality</span>
                <span class="detail-value">{{ selectedGuest.nationality || '—' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Total Bookings</span>
                <span class="detail-value">{{ selectedGuest.totalBookings }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Last Stay</span>
                <span class="detail-value">{{ selectedGuest.lastStay || '—' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Guest Since</span>
                <span class="detail-value">{{ selectedGuest.createdAt }}</span>
              </div>
            </div>

            <!-- Booking History -->
            <div class="booking-history">
              <div class="history-label">Booking History</div>
              <div v-if="selectedGuest.bookings && selectedGuest.bookings.length" class="history-list">
                <div
                  v-for="b in selectedGuest.bookings"
                  :key="b.id"
                  class="history-item"
                >
                  <div class="history-left">
                    <span class="history-id">#{{ b.id }}</span>
                    <span class="history-room">{{ b.room }}</span>
                  </div>
                  <div class="history-right">
                    <span class="history-dates">{{ b.checkIn }} → {{ b.checkOut }}</span>
                    <span class="badge" :class="bookingBadgeClass(b.status)">{{ b.status }}</span>
                  </div>
                </div>
              </div>
              <div v-else class="history-empty">No bookings on record.</div>
            </div>

            <div class="modal-actions">
              <button class="btn btn-secondary" @click="closeModal">Close</button>
            </div>
          </template>

          <!-- ADD WALK-IN MODE -->
          <template v-else-if="modalMode === 'add'">
            <div class="modal-header-simple">
              <div class="modal-icon">🚶</div>
              <h3 class="modal-title">Add Walk-in Guest</h3>
              <p class="modal-text">Register a new walk-in guest into the system.</p>
            </div>
            <div class="modal-form">
              <div class="form-group">
                <label class="form-label">Full Name <span class="required">*</span></label>
                <input class="form-input" type="text" v-model="addForm.name" placeholder="e.g. Juan Dela Cruz" />
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Email <span class="optional">(optional)</span></label>
                  <input class="form-input" type="email" v-model="addForm.email" placeholder="guest@email.com" />
                </div>
                <div class="form-group">
                  <label class="form-label">Phone <span class="optional">(optional)</span></label>
                  <input class="form-input" type="text" v-model="addForm.phone" placeholder="+63 9XX XXX XXXX" />
                </div>
                <div class="form-group">
                  <label class="form-label">Check-in Date <span v-if="needsBookingDates" class="required-dates">(Required)</span></label>
                  <input class="form-input" :class="{ 'date-required': needsBookingDates }" type="date" v-model="addForm.check_in" />
                  <span v-if="needsBookingDates" class="date-helper">Dates needed for booking</span>
                </div>
                <div class="form-group">
                  <label class="form-label">Check-out Date <span v-if="needsBookingDates" class="required-dates">(Required)</span></label>
                  <input class="form-input" :class="{ 'date-required': needsBookingDates }" type="date" v-model="addForm.check_out" />
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Nationality <span class="optional">(optional)</span></label>
                <input class="form-input" type="text" v-model="addForm.nationality" placeholder="e.g. Filipino" />
              </div>
              <div class="form-group">
                <label class="form-label">Assign Room <span class="optional">(optional)</span></label>
                <select class="form-input room-select" v-model="addForm.roomId">
                  <option value="">— Select a room —</option>
                  <optgroup label="✅ Available Rooms">
                    <option
                      v-for="room in roomsWithAvailability.filter(r => r.isAvailable)"
                      :key="room.id"
                      :value="room.id"
                    >
                      Room {{ room.number }} · {{ room.name }} · {{ room.type }} · ₱{{ room.price_per_night.toLocaleString() }}/night · up to {{ room.capacity }} guests
                    </option>
                  </optgroup>
                  <optgroup label="🚫 Not Available / Under Renovation">
                    <option
                      v-for="room in roomsWithAvailability.filter(r => !r.isAvailable)"
                      :key="room.id"
                      :value="room.id"
                      disabled
                    >
                      Room {{ room.number }} · {{ room.name }} · {{ room.type }} · ₱{{ room.price_per_night.toLocaleString() }}/night · up to {{ room.capacity }} guests
                    </option>
                  </optgroup>
                </select>
                <div v-if="selectedRoom" class="room-preview" :class="{ 'room-preview-unavailable': !selectedRoom.isAvailable }">
                  <template v-for="room in roomsWithAvailability" :key="room.id">
                    <div v-if="room.id === addForm.roomId" class="room-preview-inner">
                      <span class="room-preview-number">Room {{ room.number }}</span>
                      <span class="room-preview-name">{{ room.name }}</span>
                      <span class="room-type-pill" :class="{ 'room-type-pill-unavailable': !room.isAvailable }">{{ room.type }}</span>
                      <span v-if="!room.isAvailable" class="room-unavailable-tag">🚫 Not Available</span>
                      <span v-else class="room-preview-price">₱{{ room.price_per_night.toLocaleString() }}/night</span>
                      <span class="room-preview-cap">👥 Up to {{ room.capacity }}</span>
                    </div>
                  </template>
                </div>
              </div>
            </div>
            <div class="modal-actions">
              <button class="btn btn-secondary" @click="closeModal">Cancel</button>
              <button class="btn btn-outline" @click="addForm.createBooking = false; saveWalkin(false)">Save Guest</button>
              <button class="btn btn-primary" @click="addForm.createBooking = true; saveWalkin(true)">Save & Create Booking</button>
            </div>
          </template>

          <!-- EDIT MODE -->
          <template v-else-if="modalMode === 'edit'">
            <div class="modal-header-simple">
              <h3 class="modal-title">Edit Guest</h3>
              <p class="modal-text">Update guest information.</p>
            </div>
            <div class="modal-form">
              <div class="form-group">
                <label class="form-label">Full Name</label>
                <input class="form-input" type="text" v-model="editForm.name" />
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Email</label>
                  <input class="form-input" type="email" v-model="editForm.email" />
                </div>
                <div class="form-group">
                  <label class="form-label">Phone</label>
                  <input class="form-input" type="text" v-model="editForm.phone" />
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Nationality</label>
                <input class="form-input" type="text" v-model="editForm.nationality" />
              </div>
            </div>
            <div class="modal-actions">
              <button class="btn btn-secondary" @click="closeModal">Cancel</button>
              <button class="btn btn-primary" @click="saveEdit">Save Changes</button>
            </div>
          </template>

          <!-- DELETE MODE -->
          <template v-else-if="modalMode === 'delete'">
            <div class="modal-icon">🗑️</div>
            <h3 class="modal-title">Delete Guest?</h3>
            <p class="modal-text">
              This will permanently delete <strong>{{ selectedGuest.name }}</strong> from the system.
              This action cannot be undone.
            </p>
            <div class="modal-actions modal-actions-center">
              <button class="btn btn-secondary" @click="closeModal">Go Back</button>
              <button class="btn btn-danger" @click="deleteGuest">Yes, Delete</button>
            </div>
          </template>

          <!-- BLACKLIST MODE -->
          <template v-else-if="modalMode === 'blacklist'">
            <div class="modal-icon">🚫</div>
            <h3 class="modal-title">Blacklist Guest?</h3>
            <p class="modal-text">
              This will mark <strong>{{ selectedGuest.name }}</strong> as blacklisted.
              They will be flagged on any future bookings.
            </p>
            <div class="modal-actions modal-actions-center">
              <button class="btn btn-secondary" @click="closeModal">Go Back</button>
              <button class="btn btn-danger" @click="blacklistGuest">Yes, Blacklist</button>
            </div>
          </template>

        </div>
      </div>
    </Transition>

  </AdminLayout>
</template>

<script>
import { router } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import AdminLayout from '../../components/AdminLayout.vue';

import viewIcon from '../../assets/view_action_logo.svg';
import editIcon from '../../assets/edit_action_logo.svg';
import trashIcon from '../../assets/trash_action_logo.svg';
import cancelIcon from '../../assets/cancel_action_logo.svg';

export default {
  name: 'AdminGuests',
  components: { AdminLayout },

  props: {
    guests: {
      type: Array,
      default: () => []
    },
    rooms: {
      type: Array,
      default: () => []
    }
  },

  data() {
    return {
      filterSearch: '',
      filterType: '',
      filterStatus: '',

      showModal: false,
      modalMode: 'view',   // 'view' | 'add' | 'edit' | 'blacklist'
      selectedGuest: null,
      editForm: {},
      addForm: { name: '', email: '', phone: '', nationality: '', roomId: '', check_in: '', check_out: '', createBooking: false },

      viewIcon,
      editIcon,
      trashIcon,
      cancelIcon,

      pollingInterval: null
    }
  },

  computed: {
    onlineCount()  { return this.guests.filter(g => g.type === 'online').length },
    walkinCount()  { return this.guests.filter(g => g.type === 'walk-in').length },
    stayingCount() { return this.guests.filter(g => g.status === 'Active').length },

    filteredGuests() {
      let list = this.guests

      if (this.filterSearch) {
        const q = this.filterSearch.toLowerCase()
        list = list.filter(g =>
          g.name.toLowerCase().includes(q) ||
          (g.email && g.email.toLowerCase().includes(q))
        )
      }
      if (this.filterType)   list = list.filter(g => g.type === this.filterType)
      if (this.filterStatus) list = list.filter(g => g.status === this.filterStatus)

      return list
    },

    selectedRoom() {
      if(!this.addForm.roomId) return null;
      return this.roomsWithAvailability.find(r => r.id === this.addForm.roomId) || null;
    },

    needsBookingDates() {
      return this.addForm.createBooking && this.addForm.roomId;
    },
    roomsWithAvailability() {
      return this.rooms.map(room => ({
        ...room,
        isAvailable: room.status === 'available'
      }));
    }
  },

  methods: {
    guestBadgeClass(status) {
      const map = {
        'Active':       'badge-checked-in',
        'Checked Out':  'badge-checked-out',
        'Blacklisted':  'badge-cancelled',
      }
      return map[status] || ''
    },

    bookingBadgeClass(status) {
      const map = {
        'Confirmed':   'badge-confirmed',
        'Pending':     'badge-pending',
        'Cancelled':   'badge-cancelled',
        'Checked In':  'badge-checked-in',
        'Checked Out': 'badge-checked-out',
      }
      return map[status] || ''
    },

    resetFilters() {
      this.filterSearch = ''
      this.filterType   = ''
      this.filterStatus = ''
    },

    openModal(guest, mode) {
      this.modalMode = mode
      if (mode === 'add') {
        this.addForm = { name: '', email: '', phone: '', nationality: '', roomId: '', check_in: '', check_out: '', createBooking: false }
      } else {
        this.selectedGuest = { ...guest }
        if (mode === 'edit') this.editForm = { ...guest }
      }
      this.showModal = true
    },

    closeModal() {
      this.showModal      = false
      this.selectedGuest  = null
      this.editForm       = {}
    },

    saveWalkin(createBooking) {
      if (!this.addForm.name.trim()) return alert('Full name is required.')

      if (createBooking && this.addForm.roomId) {
        if (!this.addForm.check_in) return alert('Check-in date is required when creating a booking.')
        if (!this.addForm.check_out) return alert('Check-out date is required when creating a booking.')
      }

      if (createBooking && !this.addForm.roomId) {
        return alert('Please select a room before creating a booking.')
      }

      this.$inertia.post('/admin/guests', {
        name:           this.addForm.name.trim(),
        email:          this.addForm.email.trim() || null,
        phone:          this.addForm.phone.trim() || null,
        nationality:    this.addForm.nationality.trim() || null,
        room_id:        this.addForm.roomId || null,
        check_in:       this.addForm.check_in || null,
        check_out:      this.addForm.check_out || null,
        create_booking: createBooking
      }, {
        onSuccess: () => this.closeModal()
      })
    },

    saveEdit() {
      this.$inertia.put(`/admin/guests/${this.editForm.id}`, {
        name:        this.editForm.name,
        email:       this.editForm.email,
        phone:       this.editForm.phone,
        nationality: this.editForm.nationality,
      }, {
        onSuccess: () => this.closeModal()
      })
    },

    deleteGuest() {
      this.$inertia.delete(`/admin/guests/${this.selectedGuest.id}`, {
        onSuccess: ()=> this.closeModal()
      })
    },

    blacklistGuest() {
      this.$inertia.patch(`/admin/guests/${this.selectedGuest.id}/blacklist`, {}, {
        onSuccess: () => this.closeModal()
      })
    },
  },

  onMounted() {
    // Start polling when component is ready
    this.pollingInterval = setInterval(() => {
      router.reload({ only: ['guests'], preserveScroll: true });
    }, 10000);
  },

  onUnmounted() {
    // Clean up when leaving the page
    clearInterval(this.pollingInterval);
  }
}

</script>

<style scoped>
/* ─── Page Header ────────────────────── */
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 24px;
}

.page-title {
  font-size: 22px;
  font-weight: 700;
  color: var(--admin-text, #111827);
  margin: 0 0 4px;
}

.page-subtitle {
  font-size: 13.5px;
  color: var(--admin-text-muted, #6B7280);
  margin: 0;
}

.btn-add-guest {
  height: 38px;
  padding: 0 18px;
  background: var(--admin-blue, #00B4FF);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
  white-space: nowrap;
}

.btn-add-guest:hover {
  background: #0099D8;
}

/* ─── Stats Row ──────────────────────── */
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 22px;
}

.stat-card {
  background: #fff;
  border: 1px solid var(--admin-border, #E5E7EB);
  border-radius: 12px;
  padding: 18px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.stat-icon {
  font-size: 26px;
  line-height: 1;
}

.stat-logo {
  width: 32px;
  height: 32px;
  display: block;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: var(--admin-text, #111827);
  line-height: 1;
}

.stat-label {
  font-size: 12px;
  color: var(--admin-text-muted, #6B7280);
  margin-top: 3px;
}

.stat-info {
  text-align: center;
}

/* ─── Filter Bar ─────────────────────── */
.filter-bar {
  display: flex;
  align-items: flex-end;
  gap: 14px;
  margin-bottom: 18px;
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

.filter-select,
.filter-input {
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

.filter-select:focus,
.filter-input:focus {
  border-color: var(--admin-blue, #00B4FF);
}

.search-input {
  width: 220px;
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
  align-self: flex-end;
}

.filter-reset:hover {
  background: #FEE2E2;
  border-color: #FECACA;
  color: #DC2626;
}

/* ─── Table Card ─────────────────────── */
.admin-card {
  background: #fff;
  border: 1px solid var(--admin-border, #E5E7EB);
  border-radius: 12px;
  overflow: hidden;
}

.admin-table-wrap {
  overflow-x: auto;
}

.admin-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13.5px;
}

.admin-table thead tr {
  background: var(--admin-bg, #F9FAFB);
  border-bottom: 1px solid var(--admin-border, #E5E7EB);
}

.admin-table th {
  padding: 11px 16px;
  text-align: left;
  font-size: 11.5px;
  font-weight: 600;
  color: var(--admin-text-muted, #6B7280);
  text-transform: uppercase;
  letter-spacing: 0.04em;
  white-space: nowrap;
}

.admin-table td {
  padding: 13px 16px;
  border-bottom: 1px solid var(--admin-border, #E5E7EB);
  vertical-align: middle;
}

.admin-table tbody tr:last-child td {
  border-bottom: none;
}

.admin-table tbody tr:hover {
  background: #FAFAFA;
}

.td-muted  { color: var(--admin-text-muted, #6B7280); font-size: 13px; }
.td-center { text-align: center; font-weight: 600; color: var(--admin-text, #111827); }

/* ─── Guest Cell ─────────────────────── */
.guest-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}

.guest-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--admin-blue, #00B4FF);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  flex-shrink: 0;
}

.avatar-walkin {
  background: #F59E0B;
}

.guest-name  { font-size: 13.5px; font-weight: 500; color: var(--admin-text, #111827); }
.guest-email { font-size: 11.5px; color: var(--admin-text-muted, #6B7280); }

/* ─── Type Badge ─────────────────────── */
.type-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 600;
  white-space: nowrap;
}

.type-online {
  background: #EFF6FF;
  color: #2563EB;
}

.type-walkin {
  background: #FEF3C7;
  color: #D97706;
}

/* ─── Status Badges ──────────────────── */
.badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 600;
  white-space: nowrap;
}

.badge-confirmed   { background: #ECFDF5; color: #059669; }
.badge-pending     { background: #FEF3C7; color: #D97706; }
.badge-cancelled   { background: #FEF2F2; color: #DC2626; }
.badge-checked-in  { background: #EFF6FF; color: #2563EB; }
.badge-checked-out { background: #F3F4F6; color: #6B7280; }

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

.action-btn:hover { transform: translateY(-1px); }
.action-btn img {
  width: 14px;
  height: 14px;
  object-fit: contain;
}
.action-btn.view:hover      { background: #EFF6FF; border-color: #BFDBFE; }
.action-btn.edit:hover      { background: #FFFBEB; border-color: #FDE68A; }
.action-btn.blacklist:hover { background: #FEF2F2; border-color: #FECACA; }

/* ─── Empty State ────────────────────── */
.empty-state {
  padding: 48px;
  text-align: center;
}

.empty-icon  { font-size: 32px; margin-bottom: 10px; }
.empty-title { font-size: 15px; font-weight: 600; color: var(--admin-text, #111827); margin-bottom: 4px; }
.empty-text  { font-size: 13px; color: var(--admin-text-muted, #6B7280); }

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
  max-width: 95vw;
  max-height: 85vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.modal-wide {
  width: 560px;
}

.modal-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 22px;
  gap: 12px;
}

.modal-guest-profile {
  display: flex;
  align-items: center;
  gap: 14px;
}

.modal-avatar {
  width: 46px;
  height: 46px;
  border-radius: 50%;
  background: var(--admin-blue, #00B4FF);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 700;
  flex-shrink: 0;
}

.modal-title {
  font-size: 17px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 6px;
}

.modal-header-simple {
  text-align: center;
  margin-bottom: 22px;
}

.modal-header-simple .modal-title {
  margin-bottom: 6px;
}

.modal-icon {
  font-size: 36px;
  margin-bottom: 10px;
}

.modal-text {
  font-size: 13.5px;
  color: #6B7280;
  margin: 0;
  line-height: 1.6;
}

/* ─── Detail Grid ────────────────────── */
.modal-detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-bottom: 22px;
}

.detail-item  { display: flex; flex-direction: column; gap: 3px; }
.detail-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #9CA3AF; }
.detail-value { font-size: 13.5px; font-weight: 500; color: #111827; }

/* ─── Booking History ────────────────── */
.booking-history {
  margin-bottom: 22px;
}

.history-label {
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #9CA3AF;
  margin-bottom: 10px;
}

.history-list { display: flex; flex-direction: column; gap: 8px; }

.history-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: var(--admin-bg, #F9FAFB);
  border-radius: 8px;
  border: 1px solid var(--admin-border, #E5E7EB);
  gap: 12px;
}

.history-left  { display: flex; align-items: center; gap: 10px; }
.history-right { display: flex; align-items: center; gap: 10px; }

.history-id {
  font-family: monospace;
  font-size: 11.5px;
  color: var(--admin-blue, #00B4FF);
  font-weight: 600;
}

.history-room  { font-size: 13px; color: var(--admin-text, #111827); font-weight: 500; }
.history-dates { font-size: 11.5px; color: var(--admin-text-muted, #6B7280); }
.history-empty { font-size: 13px; color: #9CA3AF; font-style: italic; }

/* ─── Modal Form ─────────────────────── */
.modal-form { margin-bottom: 22px; }

.form-group { margin-bottom: 14px; }

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

.required { color: #DC2626; }
.optional  { font-weight: 400; text-transform: none; font-size: 11px; color: #9CA3AF; }

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

.form-input:focus { border-color: var(--admin-blue, #00B4FF); }

.date-required {
  border-color: #F59E0B !important;
  background: #FFFBEB;
}

.date-required:focus {
  border-color: #D97706 !important;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
}

.required-dates {
  color: #D97706;
  font-weight: 700;
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.date-helper {
  display: block;
  font-size: 11px;
  color: #D97706;
  margin-top: 4px;
  font-weight: 500;
}

/* ─── Modal Actions ──────────────────── */
.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.modal-actions-center { justify-content: center; }

.btn {
  padding: 8px 18px;
  border-radius: 8px;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
  border: none;
}

.btn-secondary {
  background: var(--admin-bg, #F3F4F6);
  color: var(--admin-text, #374151);
  border: 1px solid var(--admin-border, #E5E7EB);
}

.btn-secondary:hover { background: #E5E7EB; }

.btn-outline {
  background: #fff;
  color: var(--admin-blue, #00B4FF);
  border: 1.5px solid var(--admin-blue, #00B4FF);
}

.btn-outline:hover { background: #EFF6FF; }

.btn-primary {
  background: var(--admin-blue, #00B4FF);
  color: #fff;
}

.btn-primary:hover { background: #0099D8; }

.btn-danger {
  background: #DC2626;
  color: #fff;
}

.btn-danger:hover { background: #B91C1C; }

/* ─── Room Select Preview ────────────── */
.room-select {
  cursor: pointer;
}

.room-preview {
  margin-top: 8px;
  border: 1px solid #BFDBFE;
  border-radius: 8px;
  background: #EFF6FF;
  padding: 10px 14px;
}

.room-preview-inner {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.room-preview-number {
  font-family: monospace;
  font-size: 11.5px;
  font-weight: 700;
  color: var(--admin-blue, #00B4FF);
}

.room-preview-name {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
}

.room-type-pill {
  background: #DBEAFE;
  color: #2563EB;
  border-radius: 20px;
  padding: 2px 8px;
  font-size: 11px;
  font-weight: 600;
}

.room-preview-unavailable {
  border-color: #FECACA;
  background: #FEF2F2;
}

.room-unavailable-tag {
  font-size: 12px;
  font-weight: 700;
  color: #DC2626;
  margin-left: auto;
}

.room-type-pill-unavailable {
  background: #FEE2E2;
  color: #DC2626;
}

.room-preview-price {
  font-size: 12.5px;
  font-weight: 700;
  color: #059669;
  margin-left: auto;
}

.room-preview-cap {
  font-size: 11.5px;
  color: var(--admin-text-muted, #6B7280);
}

/* ─── Fade Transition ────────────────── */
.fade-enter-active,
.fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from,
.fade-leave-to     { opacity: 0; }
</style>