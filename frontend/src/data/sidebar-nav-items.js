export default function() {
  return [
    {
      title: "Tổng quan",
      to: "/dashboard",
      htmlBefore: '<i class="material-icons">dashboard</i>',
      htmlAfter: ""
    },
    {
      title: "Quản lý phòng",
      htmlBefore: '<i class="material-icons">meeting_room</i>',
      to: "/rooms",
    },
    {
      title: "Quản lý thiết bị",
      htmlBefore: '<i class="material-icons">devices_other</i>',
      to: "/devices",
    },
    {
      title: "Quản lý cảm biến",
      htmlBefore: '<i class="material-icons">bug_report</i>',
      to: "/sensors",
    },
    {
      title: "Quản lý người dùng",
      htmlBefore: '<i class="material-icons">people</i>',
      to: "/users",
    },
  ];
}
