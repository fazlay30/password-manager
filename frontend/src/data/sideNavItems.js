import { MdSpaceDashboard } from "react-icons/md";
import { FaBox } from "react-icons/fa6";
import { IoBagCheck } from "react-icons/io5";
import { FaUserAlt } from "react-icons/fa";
import { IoSettings } from "react-icons/io5";

export const SidebarTopItems = [
  {
    id: 1,
    path: "/dashboard",
    icon: <MdSpaceDashboard />,
    title: "Dashboard"
  },
  {
    id: 2,
    path: "/my-groups",
    icon: <FaBox />,
    title: "My Groups"
  },
  // {
  //   id: 3,
  //   path: "/my-sales",
  //   icon: <IoBagCheck />,
  //   title: "My Sales"
  // }
]

export const SidebarBottomItems = [
  {
    id: 1,
    path: "/my-profile",
    icon: <FaUserAlt />,
    title: "My Profile"
  },
  // {
  //   id: 2,
  //   path: "/settings",
  //   icon: <IoSettings />,
  //   title: "Settings"
  // }
  {
    id: 3,
    path: "/logout",
    icon: <FaUserAlt />,
    title: "Logout"
  },
]