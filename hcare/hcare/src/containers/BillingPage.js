// src/containers/BillingPage.js
import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import {
  Table,
  Card,
  Tag,
  Typography,
  Button,
  Modal,
  Descriptions,
} from "antd";
import { useNavigate } from "react-router-dom";

const { Title } = Typography;

export default function BillingPage() {
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const invoices = useSelector((s) => s.billing?.list || []);

  // ✅ Use Redux selectors for doctors and patients
  const { list: doctors } = useSelector((s) => s.doctors);
  const { list: patients } = useSelector((s) => s.patients);

  const [isModalVisible, setIsModalVisible] = useState(false);
  const [selectedInvoice, setSelectedInvoice] = useState(null);

  useEffect(() => {
    dispatch({ type: "billing/fetchStart" });
    if (doctors.length === 0) dispatch({ type: "doctors/fetchStart" });
    if (patients.length === 0) dispatch({ type: "patients/fetchStart" });
  }, [dispatch, doctors.length, patients.length]);

  const findPatient = (id) =>
    patients.find((p) => String(p.id) === String(id));

  const findDoctor = (id) =>
    doctors.find((d) => String(d.id) === String(id));

  const getPatientName = (id) =>
    findPatient(id)?.name || "Unknown Patient";

  const getDoctorName = (id) =>
    findDoctor(id)?.name || "Unknown Doctor";

  const handleViewDetails = (invoice) => {
    setSelectedInvoice(invoice);
    setIsModalVisible(true);
  };

  const handlePay = (invoice) => {
    setIsModalVisible(false);
    navigate("/payment", { state: { invoice } });
  };

  const columns = [
    { title: "Invoice ID", dataIndex: "id" },

    {
      title: "Patient",
      render: (_, rec) => getPatientName(rec.patientId),
    },

    {
      title: "Doctor",
      render: (_, rec) => getDoctorName(rec.doctorId),
    },

    {
      title: "Amount",
      render: (_, rec) => {
        // ✅ SAFE OBJECT HANDLING
        if (typeof rec.amount === "object") {
          return `₹${rec.amount.amount}`;
        }
        return `₹${rec.amount}`;
      },
    },

    {
      title: "Status",
      render: (_, rec) => {
        const status =
          typeof rec.status === "object"
            ? rec.status.status
            : rec.status;

        return (
          <Tag color={status === "Paid" ? "green" : "red"}>
            {status}
          </Tag>
        );
      },
    },

    {
      title: "Action",
      render: (_, record) => (
        <Button type="primary" onClick={() => handleViewDetails(record)}>
          View
        </Button>
      ),
    },
  ];

  return (
    <div>
      <Title level={2}>Billing</Title>

      <Card>
        <Table
          dataSource={Array.isArray(invoices) ? invoices : []}
          columns={columns}
          rowKey="id"
        />
      </Card>

      <Modal
        title="Invoice Details"
        open={isModalVisible}
        onCancel={() => setIsModalVisible(false)}
        footer={[
          <Button key="close" onClick={() => setIsModalVisible(false)}>
            Close
          </Button>,
          selectedInvoice?.status !== "Paid" && (
            <Button
              key="pay"
              type="primary"
              onClick={() => handlePay(selectedInvoice)}
            >
              Pay
            </Button>
          ),
        ]}
      >
        {selectedInvoice && (
          <Descriptions bordered column={1}>
            <Descriptions.Item label="Patient">
              {getPatientName(selectedInvoice.patientId)}
            </Descriptions.Item>

            <Descriptions.Item label="Doctor">
              {getDoctorName(selectedInvoice.doctorId)}
            </Descriptions.Item>

            <Descriptions.Item label="Amount">
              ₹{typeof selectedInvoice.amount === "object"
                ? selectedInvoice.amount.amount
                : selectedInvoice.amount}
            </Descriptions.Item>

            <Descriptions.Item label="Status">
              {typeof selectedInvoice.status === "object"
                ? selectedInvoice.status.status
                : selectedInvoice.status}
            </Descriptions.Item>
          </Descriptions>
        )}
      </Modal>
    </div>
  );
}
