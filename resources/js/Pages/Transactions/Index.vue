<template>
  <a href="/">Главная</a>
  <div>
    <h1>Транзакции</h1>
    <form @submit.prevent="filterTransactions">
      <input v-model="startDate" type="date" placeholder="Начальная дата" />
      <input v-model="endDate" type="date" placeholder="Конечная дата" />
      <button type="submit">Фильтровать</button>
    </form>

    <button @click="showAllTransactions" class="mt-4">Показать все транзакции</button>

    <div>
      <h2>Общий доход: {{ income }}</h2>
      <h2>Общие расходы: {{ expenses }}</h2>
    </div>

    <table>
      <thead>
        <tr>
          <th>Тип</th>
          <th>Сумма</th>
          <th>Дата</th>
          <th>Описание</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="transaction in transactions" :key="transaction.id">
          <td>{{ transaction.type }}</td>
          <td>{{ transaction.amount }}</td>
          <td>{{ transaction.date }}</td>
          <td>{{ transaction.description }}</td>
          <td>
            <button @click="openEditModal(transaction)">Редактировать</button>
            <button @click="deleteTransaction(transaction.id)">Удалить</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Форма добавления транзакции -->
    <form @submit.prevent="addTransaction">
      <select v-model="newTransaction.type">
        <option value="income">Доход</option>
        <option value="expense">Расход</option>
      </select>
      <input v-model="newTransaction.amount" type="number" placeholder="Сумма" />
      <input v-model="newTransaction.date" type="date" />
      <input v-model="newTransaction.description" type="text" placeholder="Описание" />
      <button type="submit">Добавить</button>
    </form>

    <!-- Модальное окно для редактирования -->
    <div v-if="isEditModalOpen" class="modal">
      <div class="modal-content">
        <h2>Редактировать транзакцию</h2>
        <form @submit.prevent="updateTransaction">
          <select v-model="editedTransaction.type">
            <option value="income">Доход</option>
            <option value="expense">Расход</option>
          </select>
          <input v-model="editedTransaction.amount" type="number" placeholder="Сумма" />
          <input v-model="editedTransaction.date" type="date" />
          <input v-model="editedTransaction.description" type="text" placeholder="Описание" />
          <button type="submit">Сохранить</button>
          <button @click="closeEditModal">Отмена</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";

export default {
  props: {
    transactions: Array,
    income: Number,
    expenses: Number,
  },
  setup(props) {
    const startDate = ref("");
    const endDate = ref("");
    const newTransaction = ref({
      type: "income",
      amount: "",
      date: "",
      description: "",
    });

    const editedTransaction = ref(null);
    const isEditModalOpen = ref(false);

    const filterTransactions = () => {
      window.location.href = `/transactions?start_date=${startDate.value}&end_date=${endDate.value}`;
    };

    const addTransaction = () => {
      Inertia.post("/transactions", newTransaction.value).then(() => {
        newTransaction.value = { type: "income", amount: "", date: "", description: "" };
      });
      window.location.reload();
    };

    const openEditModal = (transaction) => {
      editedTransaction.value = { ...transaction }; // Копируем объект
      isEditModalOpen.value = true;
    };

    const closeEditModal = () => {
      isEditModalOpen.value = false;
      editedTransaction.value = null;
    };

    const updateTransaction = () => {
      Inertia.put(
        `/transactions/${editedTransaction.value.id}`,
        editedTransaction.value
      ).then(() => {
        closeEditModal();
      });
      window.location.reload();
    };

    const deleteTransaction = (id) => {
      Inertia.delete(`/transactions/${id}`);
      window.location.reload();
    };

    const showAllTransactions = () => {
      window.location.href = "/transactions";
    };

    return {
      startDate,
      endDate,
      newTransaction,
      filterTransactions,
      addTransaction,
      deleteTransaction,
      editedTransaction,
      isEditModalOpen,
      openEditModal,
      closeEditModal,
      updateTransaction,
      showAllTransactions
    };
  },
};
</script>

<style scoped>
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: fit-content;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}
</style>
