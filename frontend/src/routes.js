import React from "react";
import { Redirect } from "react-router-dom";
import authHOC from './utils/auth';

// Layout Types
import { DefaultLayout, BlankLayout } from "./layouts";

// Route Views
import Dashboard from "./views/Dashboard";
import UserProfile from "./views/UserProfile";
import ComponentsOverview from "./views/ComponentsOverview";
import Tables from "./views/Tables";
import Login from "./views/Login";

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
  {
    path: "/components-overview",
    layout: DefaultLayout,
    component: ComponentsOverview
  },
  {
    path: "/tables",
    layout: DefaultLayout,
    component: Tables
  },
];
