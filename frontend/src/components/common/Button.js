import React from "react"
import Button from "@mui/material/Button"

const CommonButton = ({
  onClick,
  variant = "outlined",
  size = "small",
  color = "primary",
  icon = null,
  text = "",
  sx = {},
  ...rest
}) => {
  return (
    <Button
      variant={variant}
      size={size}
      color={color}
      sx={{
        textTransform: "none",
        mb: "6px",
        ...sx,
      }}
      onClick={onClick}
      {...rest}
    >
      {icon}
      {text && <span>{text}</span>}
    </Button>
  )
}

export default CommonButton
