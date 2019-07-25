export default function() {
  return [
    {
      title: "Dashboard",
      to: "/dashboard",
      htmlBefore: '<i class="material-icons">dashboard</i>',
      htmlAfter: ""
    },
    {
      title: "Rooms",
      htmlBefore: '<i class="material-icons">meeting_room</i>',
      to: "/rooms",
    },
    {
      title: "Devices",
      htmlBefore: '<i class="material-icons">devices_other</i>',
      to: "/devices",
    },
    {
      title: "Users",
      htmlBefore: '<i class="material-icons">people</i>',
      to: "/users",
    },
  ];
}
