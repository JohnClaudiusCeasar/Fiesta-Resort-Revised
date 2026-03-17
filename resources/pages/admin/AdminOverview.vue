<template>
  <AdminLayout page="overview">

    <!-- Page Header -->
    <div class="page-header">
      <h1 class="page-title">Good morning, {{ firstName }} 👋</h1>
      <p class="page-subtitle">Here's what's happening at Fiesta Resort today.</p>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Today's Bookings</span>
          <div class="stat-icon">📅</div>
        </div>
        <div class="stat-value">{{ stats.todayBookings }}</div>
        <div class="stat-change up">↑ 3 from yesterday</div>
      </div>

      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Available Rooms</span>
          <div class="stat-icon">🛏️</div>
        </div>
        <div class="stat-value">{{ stats.availableRooms }}</div>
        <div class="stat-change">of {{ stats.totalRooms }} total rooms</div>
      </div>

      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Occupied Rooms</span>
          <div class="stat-icon">🔑</div>
        </div>
        <div class="stat-value">{{ stats.occupiedRooms }}</div>
        <div class="stat-change">{{ occupancyRate }}% occupancy rate</div>
      </div>

      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Total Guests</span>
          <div class="stat-icon">👥</div>
        </div>
        <div class="stat-value">{{ stats.totalGuests }}</div>
        <div class="stat-change up">↑ 12 this week</div>
      </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="admin-card">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
        <div>
          <div class="page-title" style="font-size:15px;">Recent Bookings</div>
          <div class="page-subtitle" style="font-size:12px; margin-top:2px;">Latest 5 reservations</div>
        </div>
        <Link href="/admin/bookings" class="btn btn-secondary" style="font-size:12.5px; padding:6px 14px;">
          View All →
        </Link>
      </div>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Booking ID</th>
              <th>Guest</th>
              <th>Room</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="booking in recentBookings" :key="booking.id">
              <td>
                <span style="font-family: var(--font-mono); font-size:12px; color: var(--admin-blue);">
                  #{{ booking.id }}
                </span>
              </td>
              <td>{{ booking.guest }}</td>
              <td>{{ booking.room }}</td>
              <td>{{ booking.checkIn }}</td>
              <td>{{ booking.checkOut }}</td>
              <td>
                <span class="badge" :class="badgeClass(booking.status)">
                  {{ booking.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="recentBookings.length === 0" class="empty-state">
          <div class="empty-icon">📋</div>
          <div class="empty-title">No bookings yet</div>
          <div class="empty-text">Recent reservations will appear here.</div>
        </div>
      </div>
    </div>

  </AdminLayout>
</template>

<script>
import AdminLayout from '../../components/AdminLayout.vue'
import { Link } from "@inertiajs/vue3";

export default {
  name: 'AdminOverview',
  components: { AdminLayout, Link },

  data() {
    return {
      // Placeholder stats — replace with API calls later
      stats: {
        todayBookings: 8,
        availableRooms: 12,
        occupiedRooms: 6,
        totalRooms: 18,
        totalGuests: 142,
      },

      // Placeholder recent bookings — replace with API call
      recentBookings: [
        { id: '00124', guest: 'Maria Santos',   room: 'Deluxe Suite 301',   checkIn: 'Mar 17', checkOut: 'Mar 20', status: 'Confirmed'   },
        { id: '00123', guest: 'Juan Dela Cruz',  room: 'Standard Room 102', checkIn: 'Mar 17', checkOut: 'Mar 18', status: 'Checked In'  },
        { id: '00122', guest: 'Ana Reyes',       room: 'Family Suite 205',  checkIn: 'Mar 16', checkOut: 'Mar 19', status: 'Checked In'  },
        { id: '00121', guest: 'Carlo Mendoza',   room: 'Standard Room 104', checkIn: 'Mar 15', checkOut: 'Mar 17', status: 'Checked Out' },
        { id: '00120', guest: 'Liza Fernandez',  room: 'Deluxe Room 208',   checkIn: 'Mar 18', checkOut: 'Mar 22', status: 'Pending'     },
      ]
    }
  },

  computed: {
    firstName() {
      return this.$page?.props?.user?.name?.split(' ')[0] || 'Admin'
    },

    occupancyRate() {
      if (!this.stats.totalRooms) return 0
      return Math.round((this.stats.occupiedRooms / this.stats.totalRooms) * 100)
    }
  },

  methods: {
    badgeClass(status) {
      const map = {
        'Confirmed':   'badge-confirmed',
        'Pending':     'badge-pending',
        'Cancelled':   'badge-cancelled',
        'Checked In':  'badge-checked-in',
        'Checked Out': 'badge-checked-out',
      }
      return map[status] || ''
    }
  }
}
</script>