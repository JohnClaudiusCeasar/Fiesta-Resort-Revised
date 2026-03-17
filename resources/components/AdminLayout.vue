<template>
  <div class="admin-shell">

    <!-- ── Sidebar ─────────────────────────── -->
    <aside class="admin-sidebar">

      <!-- Brand -->
      <div class="sidebar-brand">
        <a href="/admin" class="sidebar-brand-logo">
          <div class="brand-icon">🌴</div>
          <div class="brand-text">
            <span class="brand-name">Fiesta Resort</span>
            <span class="brand-sub">Admin Panel</span>
          </div>
        </a>
      </div>

      <!-- Navigation -->
      <nav class="sidebar-nav">

        <div class="nav-section-label">Main</div>

        <a
          href="/admin"
          class="nav-item"
          :class="{ active: currentPage === 'overview' }"
          @click.prevent="navigate('overview')"
        >
          <span class="nav-icon">📊</span>
          Overview
        </a>

        <div class="nav-section-label">Management</div>

        <a
          href="/admin/bookings"
          class="nav-item"
          :class="{ active: currentPage === 'bookings' }"
          @click.prevent="navigate('bookings')"
        >
          <span class="nav-icon">📅</span>
          Bookings
          <span v-if="pendingCount > 0" class="nav-badge">{{ pendingCount }}</span>
        </a>

        <a
          href="/admin/rooms"
          class="nav-item"
          :class="{ active: currentPage === 'rooms' }"
          @click.prevent="navigate('rooms')"
        >
          <span class="nav-icon">🛏️</span>
          Room Management
        </a>

        <a
          href="/admin/guests"
          class="nav-item"
          :class="{ active: currentPage === 'guests' }"
          @click.prevent="navigate('guests')"
        >
          <span class="nav-icon">👥</span>
          Guests
        </a>

      </nav>

      <!-- Footer: User + Logout -->
      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="user-avatar">{{ userInitial }}</div>
          <div class="user-info">
            <div class="user-name">{{ user.name }}</div>
            <div class="user-role">Administrator</div>
          </div>
        </div>
        <button class="btn-logout" @click="showLogoutModal = true">
          <span>🚪</span> Logout
        </button>
      </div>

    </aside>

    <!-- ── Main Area ───────────────────────── -->
    <div class="admin-main">

      <!-- Top Bar -->
      <header class="admin-topbar">
        <div class="topbar-breadcrumb">
          <span>Admin</span>
          <span class="breadcrumb-sep">›</span>
          <span class="breadcrumb-current">{{ pageTitle }}</span>
        </div>
        <div class="topbar-actions">
          <button class="topbar-icon-btn" title="Notifications">
            🔔
            <span class="notif-dot"></span>
          </button>
          <button class="topbar-icon-btn" title="Settings">⚙️</button>
        </div>
      </header>

      <!-- Page Content (slot) -->
      <main class="admin-content">
        <slot />
      </main>

    </div>

    <!-- ── Logout Modal ────────────────────── -->
    <Transition name="fade">
      <div v-if="showLogoutModal" class="modal-overlay" @click.self="showLogoutModal = false">
        <div class="modal-box">
          <div class="modal-icon">🚪</div>
          <h3 class="modal-title">Logging out?</h3>
          <p class="modal-text">You'll need to log in again to access the admin panel.</p>
          <div class="modal-actions">
            <button class="btn btn-secondary" @click="showLogoutModal = false">Cancel</button>
            <button class="btn btn-primary" @click="logout">Yes, Logout</button>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script>

export default {
  name: 'AdminLayout',

  props: {
    // Pass the current page name from each admin page
    page: {
      type: String,
      default: 'overview'
    }
  },

  data() {
    return {
        currentPage: this.page,
        showLogoutModal: false,
        pendingCount: 0,
        user: {
            name: 'Admin User',
        }
    }
},

  computed: {
    userInitial() {
      return this.user.name?.charAt(0).toUpperCase() || 'A'
    },

    pageTitle() {
      const titles = {
        overview: 'Overview',
        bookings: 'Bookings & Reservations',
        rooms:    'Room Management',
        guests:   'Guest Management',
      }
      return titles[this.currentPage] || 'Overview'
    }
  },

  methods: {
    navigate(page) {
        this.currentPage = page
    },

    logout() {
        window.location.href = '/login'
    }
}
}
</script>

<style scoped>
/* ─── Modal ──────────────────────────── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
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
  width: 360px;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.modal-icon {
  font-size: 36px;
  margin-bottom: 12px;
}

.modal-title {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 8px;
}

.modal-text {
  font-size: 13.5px;
  color: #6B7280;
  margin-bottom: 24px;
  line-height: 1.5;
}

.modal-actions {
  display: flex;
  gap: 10px;
  justify-content: center;
}

/* ─── Fade Transition ────────────────── */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>