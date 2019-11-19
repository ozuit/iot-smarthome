import React from "react";
import { Redirect } from "react-router-dom";
import authHOC from './utils/auth';

// Layout Types
import { DefaultLayout, BlankLayout } from "./layouts";

// Route Views
import Login from "./views/Login";
import UserProfile from "./views/UserProfile";
import Dashboard from "./views/Dashboard";
import Rooms from "./views/Rooms";
import NewRoom from "./components/rooms/NewRoom";
import EditRoom from "./components/rooms/EditRoom";
import ShowDevice from "./components/devices/ShowDevice";
import Devices from "./views/Devices";
import NewDevice from "./components/devices/NewDevice";
import EditDevice from "./components/devices/EditDevice";
import Users from "./views/Users";
import NewUser from "./components/users/NewUser";
import EditUser from "./components/users/EditUser";
import Sensors from "./views/Sensors";
import NewSensor from "./components/sensors/NewSensor";
import EditSensor from "./components/sensors/EditSensor";
import Setting from "./views/Setting";

export default [
  {
    path: "/",
    exact: true,
    layout: DefaultLayout,
    component: () => <Redirect to="/dashboard" />
  },
  {
    path: "/login",
    layout: BlankLayout,
    component: authHOC(Login)
  },
  {
    path: "/dashboard",
    layout: DefaultLayout,
    component: authHOC(Dashboard)
  },
  {
    path: "/user-profile",
    layout: DefaultLayout,
    component: authHOC(UserProfile)
  },
  // Rooms
  {
    path: "/rooms",
    layout: DefaultLayout,
    component: authHOC(Rooms)
  },
  {
    path: "/new-room",
    layout: DefaultLayout,
    component: authHOC(NewRoom)
  },
  {
    path: "/edit-room/:room_id",
    layout: DefaultLayout,
    component: authHOC(EditRoom)
  },
  // Devices
  {
    path: "/devices",
    layout: DefaultLayout,
    component: authHOC(Devices)
  },
  {
    path: "/new-device",
    layout: DefaultLayout,
    component: authHOC(NewDevice)
  },
  {
    path: "/edit-device/:device_id",
    layout: DefaultLayout,
    component: authHOC(EditDevice)
  },
  {
    path: "/show-device/:room_id",
    layout: DefaultLayout,
    component: authHOC(ShowDevice)
  },
  // Sensors
  {
    path: "/sensors",
    layout: DefaultLayout,
    component: authHOC(Sensors)
  },
  {
    path: "/new-sensor",
    layout: DefaultLayout,
    component: authHOC(NewSensor)
  },
  {
    path: "/edit-sensor/:sensor_id",
    layout: DefaultLayout,
    component: authHOC(EditSensor)
  },
  // Users
  {
    path: "/users",
    layout: DefaultLayout,
    component: authHOC(Users)
  },
  {
    path: "/new-user",
    layout: DefaultLayout,
    component: authHOC(NewUser)
  },
  {
    path: "/edit-user/:user_id",
    layout: DefaultLayout,
    component: authHOC(EditUser)
  },
  // Setting
  {
    path: "/setting",
    layout: DefaultLayout,
    component: authHOC(Setting)
  },
];
