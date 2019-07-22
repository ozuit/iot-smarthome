import React from "react";
import { Redirect } from "react-router-dom";
import authHOC from './utils/auth';

// Layout Types
import { DefaultLayout, BlankLayout } from "./layouts";

// Route Views
import Dashboard from "./views/Dashboard";
import UserProfileLite from "./views/UserProfileLite";
import AddNewPost from "./views/AddNewPost";
import Errors from "./views/Errors";
import ComponentsOverview from "./views/ComponentsOverview";
import Tables from "./views/Tables";
import BlogPosts from "./views/BlogPosts";
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
    path: "/user-profile-lite",
    layout: DefaultLayout,
    component: authHOC(UserProfileLite)
  },
  {
    path: "/add-new-post",
    layout: DefaultLayout,
    component: AddNewPost
  },
  {
    path: "/errors",
    layout: DefaultLayout,
    component: Errors
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
  {
    path: "/blog-posts",
    layout: DefaultLayout,
    component: BlogPosts
  }
];
