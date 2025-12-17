import React, { useEffect, useRef } from "react";
import { Form, Input, Select, DatePicker, TimePicker, Row, Col, message } from "antd";
import dayjs from "dayjs";
import { useDispatch, useSelector } from "react-redux";

export default function AppointmentForm({ initial = null, onSaved = () => {}, autoFocusPatientId }) {
  const [form] = Form.useForm();
  const dispatch = useDispatch();

  const { list: patients } = useSelector((s) => s.patients);
  const { list: doctors } = useSelector((s) => s.doctors);

  useEffect(() => {
    if (patients.length === 0) dispatch({ type: "patients/fetchStart" });
    if (doctors.length === 0) dispatch({ type: "doctors/fetchStart" });
  }, [dispatch, patients.length, doctors.length]);

  useEffect(() => {
    if (initial) {
      form.setFieldsValue({
        patientId: initial.patientId,
        doctorId: initial.doctorId,
        date: dayjs(initial.appointmentDate),
        time: initial.appointmentTime ? dayjs(initial.appointmentTime, "hh:mm A") : undefined,
        reason: initial.reason || "",
        remarks: initial.remarks || "",
        status: initial.status || "Pending",
      });
    } else {
      form.resetFields();
      if (autoFocusPatientId) form.setFieldsValue({ patientId: autoFocusPatientId });
    }
  }, [initial, form, autoFocusPatientId]);

  const onFinish = (vals) => {
    const payload = {
      patientId: vals.patientId,
      doctorId: vals.doctorId,
      appointmentDate: vals.date.format("YYYY-MM-DD"),
      appointmentTime: vals.time.format("hh:mm A"),
      reason: vals.reason,
      remarks: vals.remarks || "",
      status: vals.status || "Pending",
    };

    if (initial?.id) {
      dispatch({
        type: "appointments/updateStatus",
        payload: { appointment: { ...initial, ...payload }, status: payload.status },
      });
      message.success("Appointment updated");
    } else {
      dispatch({
        type: "appointments/createStart",
        payload,
      });
      message.success("Appointment scheduled");
    }

    onSaved();
  };

  return (
    <Form
      layout="vertical"
      form={form}
      onFinish={onFinish}
      style={{ paddingRight: 8, marginTop: "-10px" }}
    >
      <Row gutter={[12, 4]}>
        <Col span={8}>
          <Form.Item name="patientId" label="Patient" rules={[{ required: true }]} style={{ marginBottom: 8 }}>
            <Select placeholder="Select patient">
              {patients.map((p) => (
                <Select.Option key={p.id} value={p.id}>
                  {p.name}
                </Select.Option>
              ))}
            </Select>
          </Form.Item>
        </Col>

        <Col span={8}>
          <Form.Item name="doctorId" label="Doctor" rules={[{ required: true }]} style={{ marginBottom: 8 }}>
            <Select placeholder="Select doctor">
              {doctors.map((d) => (
                <Select.Option key={d.id} value={d.id}>
                  {d.name} {d.specialization ? `• ${d.specialization}` : ""}
                </Select.Option>
              ))}
            </Select>
          </Form.Item>
        </Col>

        <Col span={8}>
          <Form.Item name="date" label="Date" rules={[{ required: true }]} style={{ marginBottom: 8 }}>
            <DatePicker style={{ width: "100%" }} />
          </Form.Item>
        </Col>

        <Col span={8}>
          <Form.Item name="time" label="Time" rules={[{ required: true }]} style={{ marginBottom: 8 }}>
            <TimePicker style={{ width: "100%" }} format="hh:mm A" use12Hours />
          </Form.Item>
        </Col>

        <Col span={8}>
          <Form.Item name="status" label="Status" style={{ marginBottom: 8 }}>
            <Select>
              <Select.Option value="Pending">Pending</Select.Option>
              <Select.Option value="Completed">Completed</Select.Option>
              <Select.Option value="Cancelled">Cancelled</Select.Option>
            </Select>
          </Form.Item>
        </Col>

        <Col span={24}>
          <Form.Item name="reason" label="Reason" rules={[{ required: true }]} style={{ marginBottom: 8 }}>
            <Input placeholder="Appointment reason" />
          </Form.Item>
        </Col>

        <Col span={24}>
          <Form.Item name="remarks" label="Remarks" style={{ marginBottom: 8 }}>
            <Input.TextArea placeholder="Notes" />
          </Form.Item>
        </Col>
      </Row>
    </Form>
  );
}
