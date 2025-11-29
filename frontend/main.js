const API_BASE_URL = "../api.php";

function showMessage(message, type = "success") {
  const feedbackContainer = document.getElementById("feedback");
  if (!feedbackContainer) return;

  feedbackContainer.innerHTML = "";

  const messageElement = document.createElement("div");
  messageElement.className =
    "feedback-message " +
    (type === "success" ? "feedback-success" : "feedback-error");
  messageElement.textContent = message;

  feedbackContainer.appendChild(messageElement);
}

async function handleEntrySubmit(event) {
  event.preventDefault();

  const plateField = document.getElementById("entry-plate");
  const typeField = document.getElementById("entry-type");

  const plate = plateField.value.trim();
  const vehicleType = typeField.value;

  if (!plate) {
    showMessage("Informe a placa para registrar a entrada.", "error");
    return;
  }

  try {
    const body = new URLSearchParams({ plate, type: vehicleType });

    const response = await fetch(`${API_BASE_URL}?action=entry`, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body,
    });

    const serverMessage = await response.text();

    if (!response.ok) {
      showMessage(
        `Erro ao registrar entrada: ${serverMessage || response.statusText}`,
        "error"
      );
      return;
    }

    showMessage(
      serverMessage || "Entrada registrada com sucesso.",
      "success"
    );

    plateField.value = "";
    await loadReport(true);
  } catch (error) {
    console.error(error);
    showMessage("Falha na comunicação com o servidor (entrada).", "error");
  }
}

async function handleExitSubmit(event) {
  event.preventDefault();

  const plateField = document.getElementById("exit-plate");
  const hoursField = document.getElementById("exit-hours");

  const plate = plateField.value.trim();
  const hours = hoursField ? hoursField.value.trim() : "";

  if (!plate) {
    showMessage("Informe a placa para registrar a saída.", "error");
    return;
  }

  try {
    const params = new URLSearchParams({ plate });

    if (hours !== "") {
      params.append("hours", hours);
    }

    const response = await fetch(`${API_BASE_URL}?action=exit`, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params,
    });

    const serverMessage = await response.text();

    if (!response.ok) {
      showMessage(
        `Erro ao registrar saída: ${serverMessage || response.statusText}`,
        "error"
      );
      return;
    }

    showMessage(serverMessage || "Saída registrada com sucesso.", "success");

    plateField.value = "";
    if (hoursField) hoursField.value = "";

    await loadReport(true);
  } catch (error) {
    console.error(error);
    showMessage("Falha na comunicação com o servidor (saída).", "error");
  }
}
async function loadReport(silent = false) {
  const tableBody = document.getElementById("report-body");
  const lastUpdateLabel = document.getElementById("report-last-update");

  if (!tableBody) return;

  if (!silent) {
    tableBody.innerHTML =
      '<tr><td colspan="3" class="table-empty">Carregando relatório...</td></tr>';
  }

  try {
    const response = await fetch(`${API_BASE_URL}?action=report`, {
      headers: { Accept: "application/json" },
    });

    const rawText = await response.text();

    let reportData = null;

    try {
      reportData = JSON.parse(rawText);
    } catch {
      console.error("Não foi possível interpretar o relatório como JSON.");
    }

    if (!Array.isArray(reportData)) {
      tableBody.innerHTML =
        '<tr><td colspan="3" class="table-empty">Não foi possível carregar os dados.</td></tr>';
      if (!silent) showMessage("Formato inesperado de relatório.", "error");
      return;
    }

    renderReportTable(reportData, tableBody);
    updateDashboardStats(reportData);

    if (lastUpdateLabel) {
      lastUpdateLabel.textContent = new Date().toLocaleString("pt-BR");
    }

    if (!silent) showMessage("Relatório atualizado com sucesso.");
  } catch (error) {
    console.error(error);
    if (!silent) {
      showMessage(
        "Não foi possível carregar o relatório. Verifique o servidor.",
        "error"
      );
    }
  }
}

function renderReportTable(data, tableBody) {
  tableBody.innerHTML = "";

  if (data.length === 0) {
    tableBody.innerHTML =
      '<tr><td colspan="3" class="table-empty">Nenhum dado disponível.</td></tr>';
    return;
  }

  for (const item of data) {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${item.type}</td>
      <td>${item.total}</td>
      <td>R$ ${Number(item.revenue || 0)
        .toFixed(2)
        .replace(".", ",")}</td>
    `;
    tableBody.appendChild(row);
  }
}

function updateDashboardStats(data) {
  let totalCar = 0;
  let totalMoto = 0;
  let totalTruck = 0;
  let totalRevenue = 0;

  data.forEach((record) => {
    const type = (record.type || "").toLowerCase();
    const total = record.total || 0;
    const revenue = record.revenue || 0;

    if (type === "carro") totalCar += total;
    if (type === "moto") totalMoto += total;
    if (type === "caminhao" || type === "caminhão") totalTruck += total;

    totalRevenue += revenue;
  });

  document.getElementById("summary-car").textContent = totalCar;
  document.getElementById("summary-moto").textContent = totalMoto;
  document.getElementById("summary-truck").textContent = totalTruck;
  document.getElementById(
    "summary-total"
  ).textContent = `R$ ${totalRevenue.toFixed(2).replace(".", ",")}`;
}

window.addEventListener("DOMContentLoaded", () => {
  const entryForm = document.getElementById("form-entry");
  const exitForm = document.getElementById("form-exit");
  const reportButton = document.getElementById("btn-relatorio");

  if (entryForm) entryForm.addEventListener("submit", handleEntrySubmit);
  if (exitForm) exitForm.addEventListener("submit", handleExitSubmit);
  if (reportButton) {
    reportButton.addEventListener("click", () => loadReport(false));
  }

  loadReport(true);
});
