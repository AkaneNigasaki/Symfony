
import React from 'react';
import { StatData, Enrollment } from './types';

const STATS: StatData[] = [
  { label: 'Total Students', value: '2,543', trend: '+12.5% from last month', isPositive: true, icon: 'groups', iconColor: 'text-blue-400 bg-blue-500/10' },
  { label: 'Active Courses', value: '48', trend: '+3 new courses', isPositive: true, icon: 'menu_book', iconColor: 'text-primary-light bg-primary/10' },
  { label: 'Total Revenue', value: '$124k', trend: '-2.4% from last month', isPositive: false, icon: 'payments', iconColor: 'text-green-400 bg-green-500/10' },
  { label: 'Certificates', value: '856', trend: '+5.2% from last month', isPositive: true, icon: 'workspace_premium', iconColor: 'text-orange-400 bg-orange-500/10' },
];

const ENROLLMENTS: Enrollment[] = [
  { name: 'Alice Freeman', course: 'Advanced React Patterns', date: 'Oct 24, 2023', status: 'Completed' },
  { name: 'Bob Smith', course: 'UI/UX Fundamentals', date: 'Oct 23, 2023', status: 'In Progress' },
  { name: 'Charlie Davis', course: 'Data Science Basics', date: 'Oct 22, 2023', status: 'Pending' },
  { name: 'Diana Ross', course: 'Introduction to Python', date: 'Oct 21, 2023', status: 'In Progress' },
];

const SidebarItem: React.FC<{ icon: string; label: string; active?: boolean; badge?: number }> = ({ icon, label, active, badge }) => (
  <a href="#" className={`group flex items-center gap-3 rounded-xl px-3 py-3 transition-all duration-200 ${active ? 'bg-surface-dark text-white' : 'text-white/60 hover:bg-surface-hover hover:text-white'}`}>
    <span className={`material-symbols-outlined ${active ? 'fill-[1]' : ''}`}>{icon}</span>
    <span className="text-sm font-medium flex-1">{label}</span>
    {badge && (
      <span className="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-white ring-2 ring-background-dark">
        {badge}
      </span>
    )}
  </a>
);

const ProfileFlyout: React.FC = () => {
  return (
    <div className="group-profile relative">
      {/* Trigger Button */}
      <button className="flex w-full items-center gap-3 rounded-2xl border border-white/5 bg-surface-dark p-3 text-left transition-all hover:bg-surface-hover hover:border-white/10 group">
        <img 
          src="https://picsum.photos/seed/sarah/100/100" 
          alt="Sarah Connors" 
          className="h-10 w-10 rounded-full object-cover ring-2 ring-white/10" 
        />
        <div className="flex flex-1 flex-col overflow-hidden">
          <p className="truncate text-sm font-semibold text-white">Sarah Connors</p>
          <p className="truncate text-xs text-white/50">Super Admin</p>
        </div>
        <span className="material-symbols-outlined text-white/40 transition-transform group-hover:translate-x-1 group-hover:text-white">chevron_right</span>
      </button>

      {/* Flyout Card */}
      <div className="flyout-menu absolute bottom-0 left-[110%] z-50 w-[320px] origin-bottom-left">
        {/* Invisible bridge to handle hover gaps */}
        <div className="absolute -left-6 top-0 bottom-0 w-6"></div>
        
        <div className="flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-[#251b2e] shadow-2xl shadow-black/80 ring-1 ring-white/5">
          {/* Cover Header */}
          <div className="relative h-28 w-full bg-gradient-to-r from-primary-dark via-purple-900 to-indigo-900 overflow-hidden">
             <div className="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
             <div className="absolute bottom-0 left-0 h-full w-full flex items-end justify-center px-6 translate-y-1/2">
                <div className="relative">
                  <img 
                    src="https://picsum.photos/seed/sarah/200/200" 
                    className="h-20 w-20 rounded-full border-4 border-[#251b2e] object-cover shadow-xl" 
                    alt="Sarah Large"
                  />
                  <div className="absolute bottom-1 right-1 h-4 w-4 rounded-full border-2 border-[#251b2e] bg-green-500"></div>
                </div>
             </div>
          </div>

          <div className="mt-12 flex flex-col items-center px-6 pb-6 text-center">
            <h2 className="text-xl font-bold text-white">Sarah Connors</h2>
            <p className="mt-1 text-sm text-white/50">sarah.admin@edumanage.io</p>
            <span className="mt-3 inline-flex items-center rounded-full bg-primary/20 px-3 py-1 text-xs font-semibold text-primary-light ring-1 ring-primary/30">
              Super Admin
            </span>
          </div>

          <div className="flex flex-col border-t border-white/5 p-2">
            {[
              { icon: 'manage_accounts', title: 'Account Settings', desc: 'Profile, security & privacy' },
              { icon: 'shield_person', title: 'Admin Controls', desc: 'Manage roles and permissions' },
              { icon: 'help_center', title: 'Support Center', desc: 'Get help with the portal' }
            ].map((item, idx) => (
              <button key={idx} className="group flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left transition-all hover:bg-surface-hover">
                <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-surface-dark text-white/50 group-hover:bg-primary group-hover:text-white transition-colors">
                  <span className="material-symbols-outlined text-[20px]">{item.icon}</span>
                </div>
                <div className="flex flex-col">
                  <span className="text-sm font-medium text-white/90 group-hover:text-white">{item.title}</span>
                  <span className="text-[11px] text-white/40 group-hover:text-white/60">{item.desc}</span>
                </div>
              </button>
            ))}
          </div>

          <div className="mt-1 border-t border-white/5 bg-black/20 p-4">
            <button className="flex w-full items-center justify-center gap-2 rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-white transition-all hover:bg-red-500/20 hover:text-red-400 hover:ring-1 hover:ring-red-500/30">
              <span className="material-symbols-outlined text-[20px]">logout</span>
              Sign Out
            </button>
            <div className="mt-3 flex justify-between px-1 text-[10px] text-white/20">
              <span>v2.4.8-stable</span>
              <a href="#" className="hover:text-white/40">Legal</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

const App: React.FC = () => {
  return (
    <div className="flex h-screen w-full overflow-hidden bg-background-dark font-display">
      {/* Sidebar Navigation */}
      <aside className="relative flex w-72 flex-col justify-between border-r border-white/5 bg-[#140d1b] p-5 shrink-0 z-50">
        <div className="flex flex-col gap-8">
          {/* Logo */}
          <div className="flex items-center gap-3 px-2">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-primary-dark shadow-lg shadow-primary/30">
              <span className="material-symbols-outlined text-white text-2xl">school</span>
            </div>
            <div className="flex flex-col">
              <h1 className="text-lg font-bold leading-tight text-white tracking-tight">EduManage</h1>
              <p className="text-[10px] font-medium text-white/40 uppercase tracking-widest">Admin Portal</p>
            </div>
          </div>

          {/* Navigation */}
          <nav className="flex flex-col gap-1.5">
            <SidebarItem icon="dashboard" label="Dashboard" active />
            <SidebarItem icon="menu_book" label="Courses" />
            <SidebarItem icon="groups" label="Students" />
            <SidebarItem icon="bar_chart" label="Reports" />
            <SidebarItem icon="calendar_month" label="Calendar" />
            <SidebarItem icon="notifications" label="Notifications" badge={3} />
          </nav>
        </div>

        {/* Profile Section with Flyout */}
        <ProfileFlyout />
      </aside>

      {/* Main Content Area */}
      <main className="flex-1 flex flex-col overflow-hidden">
        {/* Header */}
        <header className="flex h-20 items-center justify-between border-b border-white/5 bg-[#1a1122]/50 px-10 backdrop-blur-md sticky top-0 z-40">
          <h2 className="text-xl font-bold text-white">Dashboard Overview</h2>
          
          <div className="flex items-center gap-6">
            <div className="relative group">
              <span className="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/30 group-focus-within:text-primary transition-colors">search</span>
              <input 
                type="text" 
                placeholder="Search students, courses..." 
                className="h-11 w-72 rounded-full border-none bg-white/5 pl-12 pr-6 text-sm text-white placeholder-white/30 outline-none ring-1 ring-white/5 focus:ring-2 focus:ring-primary/50 transition-all"
              />
            </div>
            <button className="flex h-11 w-11 items-center justify-center rounded-full bg-white/5 text-white/60 hover:bg-white/10 hover:text-white transition-all">
              <span className="material-symbols-outlined">settings</span>
            </button>
          </div>
        </header>

        {/* Content Body */}
        <div className="flex-1 overflow-y-auto p-10 space-y-8">
          {/* Stats Grid */}
          <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            {STATS.map((stat, i) => (
              <div key={i} className="group rounded-2xl border border-white/5 bg-[#251b2e] p-6 transition-all hover:bg-[#2d2138] hover:border-white/10 hover:shadow-xl hover:shadow-black/20">
                <div className="flex items-start justify-between">
                  <div>
                    <p className="text-sm font-medium text-white/50">{stat.label}</p>
                    <h3 className="mt-2 text-3xl font-bold text-white">{stat.value}</h3>
                  </div>
                  <div className={`flex h-12 w-12 items-center justify-center rounded-xl transition-transform group-hover:scale-110 ${stat.iconColor}`}>
                    <span className="material-symbols-outlined">{stat.icon}</span>
                  </div>
                </div>
                <div className={`mt-5 flex items-center gap-1.5 text-xs font-semibold ${stat.isPositive ? 'text-green-400' : 'text-red-400'}`}>
                  <span className="material-symbols-outlined text-[16px]">
                    {stat.isPositive ? 'trending_up' : 'trending_down'}
                  </span>
                  <span>{stat.trend}</span>
                </div>
              </div>
            ))}
          </div>

          {/* Table Card */}
          <div className="rounded-2xl border border-white/5 bg-[#251b2e] overflow-hidden shadow-lg">
            <div className="flex items-center justify-between p-8 border-b border-white/5">
              <div>
                <h3 className="text-lg font-bold text-white">Recent Student Enrollments</h3>
                <p className="text-sm text-white/40">Tracking real-time registrations across all courses</p>
              </div>
              <button className="text-sm font-semibold text-primary-light hover:text-white transition-colors bg-primary/10 px-4 py-2 rounded-lg">
                View Detailed Report
              </button>
            </div>
            
            <div className="overflow-x-auto">
              <table className="w-full text-left">
                <thead className="bg-white/5 text-[11px] uppercase tracking-widest font-bold text-white/30">
                  <tr>
                    <th className="px-8 py-4">Student Name</th>
                    <th className="px-8 py-4">Course Title</th>
                    <th className="px-8 py-4">Enrollment Date</th>
                    <th className="px-8 py-4 text-center">Status</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-white/5">
                  {ENROLLMENTS.map((row, i) => (
                    <tr key={i} className="group hover:bg-white/5 transition-colors cursor-default">
                      <td className="px-8 py-5">
                        <div className="flex items-center gap-3">
                          <div className="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center text-primary-light font-bold text-xs">
                            {row.name.charAt(0)}
                          </div>
                          <span className="text-sm font-semibold text-white/90 group-hover:text-white">{row.name}</span>
                        </div>
                      </td>
                      <td className="px-8 py-5 text-sm text-white/60">{row.course}</td>
                      <td className="px-8 py-5 text-sm text-white/40 font-mono">{row.date}</td>
                      <td className="px-8 py-5 text-center">
                        <span className={`inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold tracking-tight
                          ${row.status === 'Completed' ? 'bg-green-500/10 text-green-400 ring-1 ring-green-500/30' : 
                            row.status === 'In Progress' ? 'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/30' : 
                            'bg-yellow-500/10 text-yellow-400 ring-1 ring-yellow-500/30'}`}>
                          {row.status}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>

          {/* New Stylish Insight Card */}
          <div className="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-[#2d1b36] to-[#1a1122] p-8 shadow-2xl">
            <div className="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-primary/10 blur-[80px]"></div>
            <div className="absolute -left-20 -bottom-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-[80px]"></div>
            
            <div className="relative flex flex-col md:flex-row items-center gap-8">
              <div className="flex-shrink-0">
                <div className="relative h-20 w-20 flex items-center justify-center">
                  <div className="absolute inset-0 rounded-2xl bg-primary/20 rotate-6 transition-transform group-hover:rotate-12"></div>
                  <div className="absolute inset-0 rounded-2xl bg-primary shadow-lg shadow-primary/40 flex items-center justify-center">
                    <span className="material-symbols-outlined text-white text-4xl">auto_awesome</span>
                  </div>
                </div>
              </div>
              
              <div className="flex-1 text-center md:text-left">
                <h3 className="text-xl font-bold text-white mb-2">Conseil d'administration rapide</h3>
                <p className="text-sm leading-relaxed text-white/60 max-w-2xl">
                  Pour gérer efficacement votre compte et vos permissions, survolez simplement le profil de <strong className="text-primary-light font-bold underline decoration-primary-light/30 underline-offset-4">Sarah Connors</strong> dans le coin inférieur gauche. 
                  Le menu contextuel vous offre un accès instantané aux réglages de sécurité et au centre de support.
                </p>
              </div>

              <div className="flex-shrink-0 flex gap-2">
                <button className="flex h-12 w-12 items-center justify-center rounded-xl bg-white/5 text-white/60 hover:bg-primary hover:text-white transition-all">
                  <span className="material-symbols-outlined">lightbulb</span>
                </button>
                <button className="flex items-center gap-2 rounded-xl bg-white/5 px-6 py-3 text-sm font-semibold text-white/80 hover:bg-white/10 transition-all">
                  En savoir plus
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
};

export default App;
