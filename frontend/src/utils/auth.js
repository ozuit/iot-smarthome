import React, { Component } from "react";
import { withRouter } from "react-router-dom";
import axios from "../api";
import jwtDecode from "jwt-decode";

const validate = function(history) {
  const isLoggedIn = !!window.localStorage.getItem("token");
  if (!isLoggedIn && history.location.pathname !== "/login") {
    history.replace("/login");
  }
  axios.defaults.headers.common['Authorization'] = 'Bearer ' + window.localStorage.getItem("token");
  if (isLoggedIn && (history.location.pathname === "/login" || history.location.pathname === "/")) {
    if (getRole() === 'ADMIN') {
      history.replace("/blog-overview");
    }
  }
};

/**
 * Higher-order component (HOC) to wrap restricted pages
 */
export default function authHOC(BaseComponent) {
  class Restricted extends Component {
    UNSAFE_componentWillMount() {
      this.checkAuthentication(this.props);
    }
    UNSAFE_componentWillReceiveProps(nextProps) {
      if (nextProps.location !== this.props.location) {
        this.checkAuthentication(nextProps);
      }
    }
    checkAuthentication(params) {
      const { history } = params;
      validate(history);
    }
    render() {
      return <BaseComponent {...this.props} />;
    }
  }
  return withRouter(Restricted);
}


/**
 * Store token
 */
export function storeToken(token) {
  return new Promise((resolve) => {
    window.localStorage.setItem('token', token);
    axios.defaults.headers.common['Authorization'] = 'Bearer' + token;
    return resolve(true);
  })
}

/**
 * Remove token
 */
export function removeToken() {
    window.localStorage.removeItem('token');
    window.location.href = '/login';
}

/**
 * Validate role
 */
export function checkRole(user_role) {
  let token = localStorage.getItem('token');
  if (token) {
    let token_decoded = jwtDecode(token);
    var roles = token_decoded.roles;
    var exists = false;
    for(var j = 0; j < roles.length; j++) {
      if (user_role.indexOf(roles[j]) !== -1) {
        exists = true;
      }
    }
    if (!exists) {
      return false;
    } else {
      return true;
    }
  }

  return false;
}

export function getRole() {
  let token = localStorage.getItem('token');
  if (token) {
    let token_decoded = jwtDecode(token);
    return token_decoded.roles[0];
  }
  return 'USER';
}

export function getUserID() {
  let token = localStorage.getItem('token');
  if (token) {
    let token_decoded = jwtDecode(token);
    return token_decoded.user_id;
  }
  return null;
}