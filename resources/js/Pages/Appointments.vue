<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { PhCalendar, PhPlus, PhX, PhPencil, PhTrash, PhClock, PhUser, PhScissors, PhCurrencyCircleDollar, PhCaretLeft, PhCaretRight } from '@phosphor-icons/vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useApi, localDateStr } from '@/composables/useApi';

const api = useApi();

const appointments = ref([]);
const monthAppointments = ref([]);
const customers = ref([]);
const professionals = ref([]);
const services = ref([]);
const selectedDate = ref(localDateStr());
const currentMonth = ref(new Date());
const showModal = ref(false);
const editingAppointment = ref(null);

const form = useForm({
  customer_id: '',
  professional_id: '',
  service_id: '',
  appointment_date: selectedDate.value,
  starts_at: '09:00',
});

const statusColors: Record<string, string> = {
  scheduled: 'bg-blue-600/10 text-blue-600',
  confirmed: 'bg-teal-600/10 text-teal-600',
  in_progress: 'bg-yellow-600/10 text-yellow-600',
  completed: 'bg-green-600/10 text-green-600',
  cancelled: 'bg-red-600/10 text-red-600',
  no_show: 'bg-gray-600/10 text-gray-600',
};

const statusLabels: Record<string, string> = {
  scheduled: 'Agendado',
  confirmed: 'Confirmado',
  in_progress: 'Em Andamento',
  completed: 'Finalizado',
  cancelled: 'Cancelado',
  no_show: 'Faltou',
};

const weekDays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
const monthNames = [
  'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
];

onMounted(() => {
  fetchAppointments();
  fetchMonthAppointments();
  fetchRelatedData();
});

watch(currentMonth, () => {
  fetchMonthAppointments();
});

async function fetchAppointments() {
  try {
    const data = await api.get(`/appointments?date=${selectedDate.value}`);
    appointments.value = data.data || [];
  } catch (e) {
    console.error('Erro ao buscar agenda:', e);
  }
}

async function fetchMonthAppointments() {
  try {
    const year = currentMonth.value.getFullYear();
    const month = String(currentMonth.value.getMonth() + 1).padStart(2, '0');
    const start = `${year}-${month}-01`;
    const endDate = new Date(year, currentMonth.value.getMonth() + 1, 0);
    const end = `${year}-${month}-${String(endDate.getDate()).padStart(2, '0')}`;
    const data = await api.get(`/appointments?date_from=${start}&date_to=${end}`);
    monthAppointments.value = data.data || [];
  } catch (e) {
    console.error('Erro ao buscar agenda do mes:', e);
  }
}

async function fetchRelatedData() {
  try {
    const [c, p, s] = await Promise.all([
      api.get('/customers?active=true'),
      api.get('/professionals?active=true'),
      api.get('/services?active=true'),
    ]);
    customers.value = c.data || [];
    professionals.value = p.data || [];
    services.value = s.data || [];
  } catch (e) {
    console.error('Erro ao carregar dados:', e);
  }
}

function changeDate(days: number) {
  const date = new Date(selectedDate.value);
  date.setDate(date.getDate() + days);
  selectedDate.value = localDateStr(date);
  currentMonth.value = new Date(date.getFullYear(), date.getMonth(), 1);
  fetchAppointments();
}

function selectDate(dateStr: string) {
  selectedDate.value = dateStr;
  fetchAppointments();
}

function goToToday() {
  const today = new Date();
  selectedDate.value = localDateStr(today);
  currentMonth.value = new Date(today.getFullYear(), today.getMonth(), 1);
  fetchAppointments();
}

function changeMonth(delta: number) {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + delta, 1);
}

function formatDate(dateStr: string) {
  return new Date(dateStr + 'T00:00:00').toLocaleDateString('pt-BR', { weekday: 'long', day: 'numeric', month: 'long' });
}

function getStatusClass(status: string) {
  return statusColors[status] || 'bg-gray-600/10 text-gray-600';
}

function getStatusLabel(status: string) {
  return statusLabels[status] || status;
}

async function updateStatus(appointment: any, newStatus: string) {
  try {
    await api.patch(`/appointments/${appointment.id}`, { status: newStatus });
    fetchAppointments();
    fetchMonthAppointments();
  } catch (e) {
    console.error('Erro ao atualizar status:', e);
  }
}

function openModal(appointment = null) {
  if (appointment) {
    editingAppointment.value = appointment;
    form.customer_id = appointment.customer_id;
    form.professional_id = appointment.professional_id;
    form.service_id = appointment.service_id;
    form.appointment_date = appointment.appointment_date;
    form.starts_at = appointment.starts_at ? appointment.starts_at.substring(0, 5) : '09:00';
  } else {
    editingAppointment.value = null;
    form.reset();
    form.appointment_date = selectedDate.value;
    form.starts_at = '09:00';
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingAppointment.value = null;
  form.reset();
}

async function saveAppointment() {
  try {
    if (editingAppointment.value) {
      await api.put(`/appointments/${editingAppointment.value.id}`, form.data());
    } else {
      await api.post('/appointments', form.data());
    }
    closeModal();
    fetchAppointments();
    fetchMonthAppointments();
  } catch (e: any) {
    if (e.response?.data?.errors) {
      form.setError(e.response.data.errors);
    }
    console.error('Erro ao salvar agendamento:', e);
  }
}

async function deleteAppointment(appointment: any) {
  if (confirm(`Tem certeza que deseja excluir o agendamento de ${appointment.customer?.name}?`)) {
    try {
      await api.delete(`/appointments/${appointment.id}`);
      fetchAppointments();
      fetchMonthAppointments();
    } catch (e) {
      console.error('Erro ao excluir agendamento:', e);
    }
  }
}

const filteredAppointments = computed(() => {
  return [...appointments.value].sort((a, b) => a.starts_at.localeCompare(b.starts_at));
});

// Calendario
const calendarDays = computed(() => {
  const year = currentMonth.value.getFullYear();
  const month = currentMonth.value.getMonth();
  const firstDayOfMonth = new Date(year, month, 1);
  const lastDayOfMonth = new Date(year, month + 1, 0);
  const startDay = firstDayOfMonth.getDay();
  const daysInMonth = lastDayOfMonth.getDate();

  const days = [];
  for (let i = 0; i < startDay; i++) {
    days.push({ date: null, dayNumber: null });
  }
  for (let i = 1; i <= daysInMonth; i++) {
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
    days.push({ date: dateStr, dayNumber: i });
  }
  return days;
});

function hasAppointments(dateStr: string) {
  return monthAppointments.value.some((a: any) => a.appointment_date === dateStr);
}

function appointmentCount(dateStr: string) {
  return monthAppointments.value.filter((a: any) => a.appointment_date === dateStr).length;
}

function isSelected(dateStr: string) {
  return dateStr === selectedDate.value;
}

function isToday(dateStr: string) {
  const today = localDateStr();
  return dateStr === today;
}
</script>

<template>
  <AppLayout title="Agenda">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 class="text-2xl font-bold text-text dark:text-white">Agenda</h2>
          <p class="text-muted">Gerencie os atendimentos do dia</p>
        </div>
        <div class="flex items-center gap-2">
          <button @click="goToToday" class="px-3 py-2 text-sm text-muted hover:text-teal-600 border border-border/50 rounded-xl hover:border-teal-600 transition-colors">
            Hoje
          </button>
          <button @click="openModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
            <PhPlus class="w-5 h-5" />
            Novo Agendamento
          </button>
        </div>
      </div>

      <!-- Calendar + Day View Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calendar -->
        <div class="lg:col-span-1 bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50">
          <div class="flex items-center justify-between mb-4">
            <button @click="changeMonth(-1)" class="p-1 text-muted hover:text-teal-600 transition-colors">
              <PhCaretLeft class="w-5 h-5" />
            </button>
            <h3 class="font-semibold text-text dark:text-white">
              {{ monthNames[currentMonth.getMonth()] }} {{ currentMonth.getFullYear() }}
            </h3>
            <button @click="changeMonth(1)" class="p-1 text-muted hover:text-teal-600 transition-colors">
              <PhCaretRight class="w-5 h-5" />
            </button>
          </div>

          <div class="grid grid-cols-7 gap-1 text-center mb-2">
            <div v-for="day in weekDays" :key="day" class="text-xs font-medium text-muted py-1">
              {{ day }}
            </div>
          </div>

          <div class="grid grid-cols-7 gap-1">
            <div
              v-for="(day, idx) in calendarDays"
              :key="idx"
              class="aspect-square flex flex-col items-center justify-center rounded-xl text-sm relative cursor-pointer transition-all"
              :class="[
                day.date
                  ? isSelected(day.date)
                    ? 'bg-teal-600 text-white shadow-md'
                    : isToday(day.date)
                      ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300 border border-teal-200 dark:border-teal-800'
                      : 'hover:bg-secondary/50 text-text dark:text-white'
                  : ''
              ]"
              @click="day.date && selectDate(day.date)"
            >
              <span v-if="day.dayNumber" class="font-medium">{{ day.dayNumber }}</span>
              <span
                v-if="day.date && hasAppointments(day.date)"
                class="absolute bottom-1 w-1.5 h-1.5 rounded-full"
                :class="isSelected(day.date) ? 'bg-white' : 'bg-teal-600'"
              ></span>
            </div>
          </div>

          <div class="mt-4 pt-4 border-t border-border/30 flex items-center gap-4 text-xs text-muted">
            <div class="flex items-center gap-1">
              <span class="w-2 h-2 rounded-full bg-teal-600"></span>
              <span>Com agendamento</span>
            </div>
            <div class="flex items-center gap-1">
              <span class="w-2 h-2 rounded-full bg-teal-600/20 border border-teal-600"></span>
              <span>Hoje</span>
            </div>
          </div>
        </div>

        <!-- Day Appointments -->
        <div class="lg:col-span-2 space-y-3">
          <div class="bg-white dark:bg-zinc-900 rounded-2xl p-4 border border-border/50 flex items-center justify-between">
            <button @click="changeDate(-1)" class="p-2 text-muted hover:text-teal-600 transition-colors">
              <PhCaretLeft class="w-5 h-5" />
            </button>
            <div class="text-center">
              <h3 class="text-lg font-semibold text-text dark:text-white">{{ formatDate(selectedDate) }}</h3>
              <p v-if="appointments.length > 0" class="text-xs text-muted">{{ appointments.length }} agendamento(s)</p>
            </div>
            <button @click="changeDate(1)" class="p-2 text-muted hover:text-teal-600 transition-colors">
              <PhCaretRight class="w-5 h-5" />
            </button>
          </div>

          <div
            v-for="appointment in filteredAppointments"
            :key="appointment.id"
            class="bg-white dark:bg-zinc-900 rounded-2xl p-6 border border-border/50"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-start gap-4">
                <div class="w-16 text-center">
                  <div class="text-2xl font-bold text-teal-600">{{ appointment.starts_at.substring(0, 5) }}</div>
                  <div class="text-xs text-muted">{{ appointment.ends_at ? appointment.ends_at.substring(0, 5) : '' }}</div>
                </div>
                <div>
                  <h3 class="font-semibold text-text dark:text-white">{{ appointment.customer?.name }}</h3>
                  <p class="text-sm text-muted">{{ appointment.service?.name }}</p>
                  <div class="flex items-center gap-2 mt-1 text-xs text-muted">
                    <span class="flex items-center gap-1">
                      <PhUser class="w-3 h-3" />
                      {{ appointment.professional?.name }}
                    </span>
                    <span class="flex items-center gap-1">
                      <PhCurrencyCircleDollar class="w-3 h-3" />
                      {{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(appointment.service_price) }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span :class="getStatusClass(appointment.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getStatusLabel(appointment.status) }}
                </span>
                <button @click="openModal(appointment)" class="p-2 text-muted hover:text-teal-600 transition-colors">
                  <PhPencil class="w-4 h-4" />
                </button>
                <button @click="deleteAppointment(appointment)" class="p-2 text-muted hover:text-red-600 transition-colors">
                  <PhTrash class="w-4 h-4" />
                </button>
              </div>
            </div>

            <!-- Status Actions -->
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-border/50">
              <button v-if="appointment.status === 'scheduled'" @click="updateStatus(appointment, 'confirmed')" class="px-3 py-1 text-xs bg-teal-600/10 text-teal-600 rounded-lg hover:bg-teal-600/20">
                Confirmar
              </button>
              <button v-if="appointment.status === 'confirmed'" @click="updateStatus(appointment, 'in_progress')" class="px-3 py-1 text-xs bg-yellow-600/10 text-yellow-600 rounded-lg hover:bg-yellow-600/20">
                Iniciar
              </button>
              <button v-if="appointment.status === 'in_progress'" @click="updateStatus(appointment, 'completed')" class="px-3 py-1 text-xs bg-green-600/10 text-green-600 rounded-lg hover:bg-green-600/20">
                Finalizar
              </button>
              <button v-if="!['completed', 'cancelled'].includes(appointment.status)" @click="updateStatus(appointment, 'no_show')" class="px-3 py-1 text-xs bg-gray-600/10 text-gray-600 rounded-lg hover:bg-gray-600/20">
                Faltou
              </button>
              <button v-if="!['completed', 'cancelled'].includes(appointment.status)" @click="updateStatus(appointment, 'cancelled')" class="px-3 py-1 text-xs bg-red-600/10 text-red-600 rounded-lg hover:bg-red-600/20">
                Cancelar
              </button>
            </div>
          </div>

          <div v-if="appointments.length === 0" class="text-center py-12 text-muted bg-white dark:bg-zinc-900 rounded-2xl border border-border/50">
            <PhCalendar class="w-12 h-12 mx-auto mb-4 opacity-50" />
            <p>Nenhum agendamento para esta data</p>
            <button @click="openModal()" class="mt-4 text-teal-600 hover:underline text-sm">
              Criar agendamento
            </button>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-xl animate-scale-in max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-text dark:text-white">
              {{ editingAppointment ? 'Editar Agendamento' : 'Novo Agendamento' }}
            </h3>
            <button @click="closeModal" class="p-2 text-muted hover:text-text">
              <PhX class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="saveAppointment" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Cliente *</label>
              <select v-model="form.customer_id" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none">
                <option value="">Selecione...</option>
                <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Profissional *</label>
              <select v-model="form.professional_id" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none">
                <option value="">Selecione...</option>
                <option v-for="p in professionals" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-text dark:text-white mb-2">Serviço *</label>
              <select v-model="form.service_id" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none">
                <option value="">Selecione...</option>
                <option v-for="s in services" :key="s.id" :value="s.id">{{ s.name }} - {{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(s.price) }}</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text dark:text-white mb-2">Data *</label>
                <input v-model="form.appointment_date" type="date" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
              </div>
              <div>
                <label class="block text-sm font-medium text-text dark:text-white mb-2">Horário *</label>
                <input v-model="form.starts_at" type="time" required class="w-full px-4 py-2 bg-secondary/30 dark:bg-zinc-800 border border-border/50 rounded-xl focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 outline-none" />
              </div>
            </div>
            <div v-if="form.errors" class="text-sm text-red-600 space-y-1">
              <p v-for="(error, key) in form.errors" :key="key">{{ Array.isArray(error) ? error[0] : error }}</p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button type="button" @click="closeModal" class="px-4 py-2 text-muted hover:text-text transition-colors">
                Cancelar
              </button>
              <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors">
                {{ editingAppointment ? 'Salvar' : 'Criar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
