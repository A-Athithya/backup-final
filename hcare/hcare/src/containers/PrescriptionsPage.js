// src/containers/PrescriptionsPage.js
import React, { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { List, Card, Descriptions, Tag, Divider, Spin } from "antd";

export default function PrescriptionsPage() {
  const dispatch = useDispatch();
  const { list, loading: prescriptionsLoading } = useSelector((s) => s.prescriptions);
  const { user, role } = useSelector((state) => state.auth);

  // ✅ Select doctors and patients from Redux
  const { list: doctors, loading: doctorsLoading } = useSelector((s) => s.doctors);
  const { list: patients, loading: patientsLoading } = useSelector((s) => s.patients);

  useEffect(() => {
    // Dispatch fetch actions for data needed (avoid direct getData)
    if (doctors.length === 0) dispatch({ type: "doctors/fetchStart" });
    if (patients.length === 0) dispatch({ type: "patients/fetchStart" });

    // Always fetch prescriptions if needed (or check list.length)
    dispatch({ type: "prescriptions/fetchStart" });
  }, [dispatch, doctors.length, patients.length]);

  const getDoctorName = (doctorId) => {
    const doc = doctors.find((d) => d.id == doctorId);
    return doc ? doc.name : "Unknown Doctor";
  };

  const getPatientName = (patientId) => {
    const pat = patients.find((p) => p.id == patientId);
    return pat ? pat.name : "Unknown Patient";
  };

  const isLoading = prescriptionsLoading || doctorsLoading || patientsLoading;

  return (
    <div style={{ padding: "12px 24px" }}>
      <h2 style={{ marginTop: 0, marginBottom: 16 }}>Prescriptions</h2>

      <Card>
        {isLoading && <div style={{ textAlign: "center", padding: 20 }}><Spin /></div>}
        {!isLoading && (
          <List
            dataSource={list || []}
            renderItem={(prescription) => (
              <List.Item>
                <Card style={{ width: "100%" }}>
                  <Descriptions
                    title={`Prescription #${prescription.id}`}
                    bordered
                    column={2}
                  >
                    <Descriptions.Item label="Patient">
                      {getPatientName(prescription.patientId)}
                    </Descriptions.Item>

                    <Descriptions.Item label="Doctor">
                      {getDoctorName(prescription.doctorId)}
                    </Descriptions.Item>

                    <Descriptions.Item label="Prescribed Date">
                      {prescription.prescribedDate}
                    </Descriptions.Item>

                    <Descriptions.Item label="Next Follow-up">
                      {prescription.nextFollowUp}
                    </Descriptions.Item>

                    <Descriptions.Item label="Status">
                      <Tag
                        color={
                          prescription.status === "Active" ? "green" : "red"
                        }
                      >
                        {prescription.status}
                      </Tag>
                    </Descriptions.Item>

                    <Descriptions.Item label="Notes">
                      {prescription.notes}
                    </Descriptions.Item>
                  </Descriptions>

                  <Divider />

                  <h4>Prescribed Medicines</h4>

                  <List
                    dataSource={prescription.medicines || []}
                    renderItem={(med) => (
                      <List.Item>
                        <div>
                          <strong>{med.medicineName}</strong> - {med.dosage},{" "}
                          {med.frequency}, Duration: {med.duration}
                        </div>
                      </List.Item>
                    )}
                    size="small"
                  />
                </Card>
              </List.Item>
            )}
          />
        )}
      </Card>
    </div>
  );
}
