<template>
  <AdminLayout page="bookings">
 
    <div class="page-header">
      <h1 class="page-title">Bookings & Reservations</h1>
      <p class="page-subtitle">Manage and monitor all guest reservations.</p>
    </div>
 
    <div class="bookings-tabs">
      <button class="bookings-tab" :class="{ active: activeTab === 'all' }" @click="activeTab = 'all'">
        All Bookings
        <span class="tab-count">{{ bookings.length }}</span>
      </button>
      <button class="bookings-tab" :class="{ active: activeTab === 'pending' }" @click="activeTab = 'pending'">
        Pending
        <span class="tab-count pending">{{ pendingBookings.length }}</span>
      </button>
    </div>
 
    <div class="filter-bar">
      <div class="filter-group">
        <label class="filter-label">Status</label>
        <select class="filter-select" v-model="filterStatus">
          <option value="">All Statuses</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Pending">Pending</option>
          <option value="Checked In">Checked In</option>
          <option value="Checked Out">Checked Out</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>
      <div class="filter-group">
        <label class="filter-label">Check-in From</label>
        <input class="filter-input" type="date" v-model="filterDateFrom" />
      </div>
      <div class="filter-group">
        <label class="filter-label">Check-in To</label>
        <input class="filter-input" type="date" v-model="filterDateTo" />
      </div>
      <button class="filter-reset" @click="resetFilters">✕ Reset</button>
    </div>
 
    <div class="admin-card">
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Guest</th>
              <th>Room</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Guests</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="booking in filteredBookings" :key="booking.id">
              <td><span class="booking-id">#{{ booking.display_id }}</span></td>
              <td>
                <div class="guest-cell">
                   <div class="guest-avatar">{{ booking.guest.charAt(0) }}</div>
                   <div>
                      <div class="guest-name">{{ booking.guest }}</div>
                      <div class="guest-email">{{ booking.email }}</div>
                   </div>
                </div>
              </td>
              <td>{{ booking.room }}</td>
              <td>{{ booking.checkIn }}</td>
              <td>{{ booking.checkOut }}</td>
              <td>{{ booking.guestCount }} Guests</td>
              <td>
                <span :class="['status-badge', badgeClass(booking.status)]">
                  {{ booking.status }}
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <button class="action-btn view" @click="openModal(booking, 'view')" title="View">👁</button>
                  <button v-if="booking.status === 'Pending'" class="action-btn confirm" @click="openModal(booking, 'confirm')" title="Confirm">✅</button>
                  <button class="action-btn edit" @click="openModal(booking, 'edit')" title="Edit">✏️</button>
                  <button class="action-btn cancel" @click="openModal(booking, 'cancel')" title="Cancel">🚫</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
 
        <div v-if="filteredBookings.length === 0" class="empty-state">
          <div class="empty-icon">📋</div>
          <div class="empty-title">No bookings found</div>
        </div>
      </div>
    </div>
 
    <ViewBookingDetailsModal 
        :show="showModal" 
        :booking="selectedBooking" 
        :mode="modalMode"
        :edit-form="editForm"
        @close="closeModal"
        @confirm="confirmBooking"
        @cancel="cancelBooking"
        @save="saveEdit"
      />
  </AdminLayout>
</template>
 
<script>
import AdminLayout from '../../components/AdminLayout.vue'
import ViewBookingDetailsModal from '../../components/ViewBookingDetailsModal.vue'
import { router } from '@inertiajs/vue3'

export default {
  name: 'AdminBookings',
  components: { AdminLayout, ViewBookingDetailsModal },
  props: {
    bookings: { type: Array, default: () => [] },
    rooms: Array,
    guests: Array
  },
  data() {
    return {
      activeTab: 'all',
      filterStatus: '',
      filterDateFrom: '',
      filterDateTo: '',
      showModal: false,
      modalMode: '',
      selectedBooking: null,
      editForm: {
        id: null,
        check_in: '', // Use database-style snake_case to match Laravel update
        check_out: '',
        notes: ''
      }
    }
  },
  computed: {
    pendingBookings() { return this.bookings.filter(b => b.status === 'Pending') },
    filteredBookings() {
      let list = this.activeTab === 'pending' ? this.pendingBookings : this.bookings
      if (this.filterStatus) list = list.filter(b => b.status === this.filterStatus)
      if (this.filterDateFrom) list = list.filter(b => b.checkIn >= this.filterDateFrom)
      if (this.filterDateTo) list = list.filter(b => b.checkIn <= this.filterDateTo)
      return list
    }
  },
  methods: {
    badgeClass(status) {
      const map = {
        'Confirmed': 'badge-confirmed',
        'Pending': 'badge-pending',
        'Cancelled': 'badge-cancelled',
        'Checked In': 'badge-checked-in',
        'Checked Out': 'badge-checked-out',
      }
      return map[status] || ''
    },
    resetFilters() {
      this.filterStatus = ''; this.filterDateFrom = ''; this.filterDateTo = ''
    },
    openModal(booking, mode) {
      this.selectedBooking = booking
      this.modalMode = mode
      if (mode === 'edit') {
        // Map camelCase prop keys to snake_case form keys for Laravel update
        this.editForm = {
          id: booking.id,
          check_in: booking.checkIn,
          check_out: booking.checkOut,
          notes: booking.notes
        }
      }
      this.showModal = true
    },
    closeModal() { this.showModal = false; this.selectedBooking = null },

    // PERSISTENT DATABASE METHODS
    confirmBooking() {
      router.patch(`/admin/bookings/${this.selectedBooking.id}/status`, { status: 'Confirmed' }, {
        onSuccess: () => this.closeModal()
      })
    },
    cancelBooking() {
      router.patch(`/admin/bookings/${this.selectedBooking.id}/status`, { status: 'Cancelled' }, {
        onSuccess: () => this.closeModal()
      })
    },
    saveEdit(formData) {
      router.put(`/admin/bookings/${formData.id}`, formData, {
        onSuccess: () => this.closeModal()
      })
    }
  }
}
</script>
 
<style scoped>
/* ─── Tabs ───────────────────────────── */
.bookings-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 18px;
}
 
.bookings-tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 18px;
  border-radius: 8px;
  border: 1px solid var(--admin-border, #E5E7EB);
  background: #fff;
  font-size: 13.5px;
  font-weight: 500;
  color: var(--admin-text-muted, #6B7280);
  cursor: pointer;
  transition: all 0.15s ease;
}
 
.bookings-tab:hover {
  background: var(--admin-bg, #F9FAFB);
}
 
.bookings-tab.active {
  background: var(--admin-blue, #00B4FF);
  color: #fff;
  border-color: var(--admin-blue, #00B4FF);
}
 
.tab-count {
  background: rgba(0,0,0,0.1);
  padding: 1px 7px;
  border-radius: 20px;
  font-size: 11.5px;
  font-weight: 600;
}
 
.bookings-tab.active .tab-count {
  background: rgba(255,255,255,0.25);
}
 
.tab-count.pending {
  background: #FEF3C7;
  color: #D97706;
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
 
/* ─── Guest Cell ─────────────────────── */
.guest-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}
 
.guest-avatar {
  width: 30px;
  height: 30px;
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
 
.guest-name {
  font-size: 13.5px;
  font-weight: 500;
  color: var(--admin-text, #111827);
}
 
.guest-email {
  font-size: 11.5px;
  color: var(--admin-text-muted, #6B7280);
}
 
.booking-id {
  font-family: var(--font-mono, monospace);
  font-size: 12px;
  color: var(--admin-blue, #00B4FF);
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
 
.action-btn:hover {
  transform: translateY(-1px);
}
 
.action-btn.view:hover   { background: #EFF6FF; border-color: #BFDBFE; }
.action-btn.confirm:hover { background: #ECFDF5; border-color: #6EE7B7; }
.action-btn.edit:hover   { background: #FFFBEB; border-color: #FDE68A; }
.action-btn.cancel:hover { background: #FEF2F2; border-color: #FECACA; }
</style>