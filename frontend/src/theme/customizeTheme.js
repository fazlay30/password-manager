import { createTheme } from "@mui/material/styles"

const theme = createTheme()

const customizeTheme = createTheme(theme, {
  palette: {
    primary: {
      main: "#2c57c1",
    },
    secondary: {
      main: "#8094ae",
    },
    warning: {
      main: "#FDA511"
    },
    grayEC: {
      main: "#ecf0f4",
    },
    white: {
      main: "#fff",
      light: "#ddd"
    },
  },
  typography: {
    pageInnerTitle: {
      fontWeight: 600,
      fontSize: "24px",
      color: "#0D0C11",
      [theme.breakpoints.down("sm")]: {
        fontSize: "18px",
      },
    },
  },

  components: {
    MuiTypography: {
      defaultProps: {
        variantMapping: {
          pageInnerTitle: "h3",
        },
      },
    },
    MuiTable: {
      styleOverrides: {
        root: {
          "th": {
            fontWeight: 600
          }
        }
      }
    },
    MuiCssBaseline: {
      styleOverrides: {
        '*': {
          fontFamily: `'DM Sans', sans-serif !important`,
        },
        body: {
          backgroundColor: '#F5F6FA',
          color: '#333'
        },
        a: {
          textDecoration: "none",
          color: "#333"
        },
      },
    },
    MuiTextField: {
      styleOverrides: {
        root: {
          ".MuiInputBase-root": {
            // padding: "10px 14px",
            borderRadius: "8px",
            fontWeight: 400,
            fontSize: "16px",
            color: "#0D0C11",
          },
        },
      },
    },
    MuiSelect: {
      styleOverrides: {
        root: {
          "&.MuiInputBase-root": {
            height: "44px",
            borderRadius: "8px",
          },
        },
      },
    },
    MuiCheckbox: {
      styleOverrides: {
        root: {
          "&.MuiInputBase-root": {
            height: "44px",
            borderRadius: "8px",
          },
          "&:not(.Mui-checked).MuiButtonBase-root": {
            color: "#A6ACB8",
          },
        },
      },
    },
    MuiStack: {
      styleOverrides: {
        root: {
          "&.MuiStack-root": {
            alignItems: "center",
            flexDirection: "row",
            flexWrap: "wrap",
          },
        },
      },
    },
    MuiFormControlLabel: {
      styleOverrides: {
        root: {
          ".MuiTypography-root": {
            fontWeight: 500,
            fontSize: "14px",
            lineHeight: "20px",
            color: "#31384A",
          },
        },
      },
    },
    MuiTooltip: {
      styleOverrides: {
        tooltip: {
          backgroundColor: "white",
          color: "#31384A",
          border: "1px solid #dadde9",
          ".MuiTooltip-arrow": {
            color: "#f1f1f1",
          },
        },
      },
    },
  },
})
// customizeTheme.typography.modalTitle = {
//   fontSize: '1.2rem',
//   '@media (min-width:600px)': {
//     fontSize: '2.5rem',
//   },
//   [customizeTheme.breakpoints.up('md')]: {
//     fontSize: '2.4rem',
//   },
// };
export const drawerWidth = 247
export const windowBreak = 960
export const mobileBreak = 600
export const layoutRightSpace = 2

export default customizeTheme
