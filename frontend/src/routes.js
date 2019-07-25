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
import Devices from "./views/Devices";
import NewDevice from "./components/devices/NewDevice";
import EditDevice from "./components/devices/EditDevice";
import Users from "./views/Users";
import NewUser from "./components/users/NewUser";
import EditUser from "./components/users/EditUser";

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
    component: Dashboard
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
    component: Rooms
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
];
